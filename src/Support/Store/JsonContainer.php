<?php

declare(strict_types=1);

/**
 * Derafu: Biblioteca PHP (Núcleo).
 * Copyright (C) Derafu <https://www.derafu.org>
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General Affero de GNU publicada por
 * la Fundación para el Software Libre, ya sea la versión 3 de la Licencia, o
 * (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero SIN
 * GARANTÍA ALGUNA; ni siquiera la garantía implícita MERCANTIL o de APTITUD
 * PARA UN PROPÓSITO DETERMINADO. Consulte los detalles de la Licencia Pública
 * General Affero de GNU para obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de GNU
 * junto a este programa.
 *
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

namespace Derafu\Lib\Core\Support\Store;

use Derafu\Lib\Core\Support\Store\Abstract\AbstractStore;
use Derafu\Lib\Core\Support\Store\Contract\JsonContainerInterface;
use InvalidArgumentException;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Helper;
use Opis\JsonSchema\Validator;
use stdClass;

/**
 * Clase para contenedor de datos estructurados con JSON Schema.
 */
class JsonContainer extends AbstractStore implements JsonContainerInterface
{
    /**
     * Configuración del schema de datos.
     *
     * @var stdClass
     */
    protected ?stdClass $schema = null;

    /**
     * Instancia que representa el formateador de los errores del validador.
     *
     * @var ErrorFormatter
     */
    private ErrorFormatter $formatter;

    /**
     * Constructor del contenedor.
     *
     * @param array $data Datos iniciales.
     * @param array $schema Schema inicial.
     */
    public function __construct(array $data = [], array $schema = [])
    {
        $this->formatter = new ErrorFormatter();
        $this->setSchema($schema);
        $data = $this->resolve($data, $this->schema);
        $this->data = $this->createFrom($data);
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema(array $schema): static
    {
        $schema = array_merge([
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
        ], $schema);

        $this->schema = Helper::toJSON($schema);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(): array
    {
        return json_decode(json_encode($this->schema), true);
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): void
    {
        $this->resolve($this->toArray(), $this->schema);
    }

    /**
     * Valida y resuelve datos usando un schema JSON.
     *
     * @param array $data Datos a validar.
     * @param stdClass $schema Schema JSON a usar.
     * @return array Datos validados y con valores por defecto aplicados.
     */
    private function resolve(array $data, stdClass $schema): array
    {
        if (!isset($schema->properties)) {
            return $data;
        }

        $data = $this->applyDefaults($data, $schema);
        $json = json_decode(json_encode($data));

        $validator = new Validator();
        $result = $validator->validate($json, $schema);

        if ($result->hasError()) {
            $errors = [];
            foreach ($this->formatter->format($result->error()) as $section => $messages) {
                foreach ($messages as $message) {
                    $errors[] = $message . ' in ' . $section . '.';
                }
            }
            throw new InvalidArgumentException(sprintf(
                'Error al validar el esquema JSON de los datos. %s',
                implode(' ', $errors)
            ));
        }

        return $data;
    }

    /**
     * Aplica valores por defecto recursivamente.
     *
     * @param array $data Datos a procesar.
     * @param stdClass $schema Schema con valores por defecto.
     * @return array Datos con valores por defecto aplicados.
     */
    private function applyDefaults(array $data, stdClass $schema): array
    {
        foreach ($schema->properties as $key => $property) {
            // Aplicar valor por defecto si la propiedad no existe.
            if (!isset($data[$key]) && isset($property->default)) {
                $data[$key] = $property->default;
            }

            // Recursión para objetos anidados.
            if (
                isset($data[$key])
                && isset($property->type)
                && $property->type === 'object'
                && isset($property->properties)
            ) {
                $data[$key] = $this->applyDefaults($data[$key], $property);
            }
        }

        return $data;
    }
}
