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
use Derafu\Lib\Core\Support\Store\Contract\DataContainerInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Clase para contenedor de datos estructurados con schema.
 */
class DataContainer extends AbstractStore implements DataContainerInterface
{
    /**
     * Configuración del schema de datos.
     *
     * @var array
     */
    protected array $schema = [];

    /**
     * Constructor del contenedor.
     *
     * @param array $data Datos iniciales.
     * @param array $schema Schema inicial.
     */
    public function __construct(array $data = [], array $schema = [])
    {
        $this->setSchema($schema);
        $this->data = $this->resolve($data, $this->schema);
    }

    /**
     * {@inheritdoc}
     */
    public function setSchema(array $schema): static
    {
        $this->schema = $schema;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): void
    {
        $this->resolve($this->data, $this->schema);
    }

    /**
     * Valida datos usando un esquema específico.
     *
     * @param array $data Datos a validar.
     * @param array $schema Esquema a usar.
     * @return array Datos validados y normalizados.
     */
    private function resolve(array $data, array $schema): array
    {
        if (empty($schema)) {
            return $data;
        }

        $resolver = new OptionsResolver();

        foreach ($schema as $key => $config) {
            // Configurar el nivel actual.
            if (!empty($config['types'])) {
                $resolver->setDefined([$key]);
                $resolver->setAllowedTypes($key, $config['types']);
            }

            if (!empty($config['required'])) {
                $resolver->setRequired([$key]);
            }

            if (!empty($config['choices'])) {
                $resolver->setAllowedValues($key, $config['choices']);
            }

            if (!empty($config['default'])) {
                $resolver->setDefault($key, $config['default']);
            }

            // Si hay un schema anidado, configurar el normalizador.
            if (!empty($config['schema'])) {
                $resolver->setDefault($key, []);
                $resolver->setAllowedTypes($key, 'array');

                $resolver->setNormalizer(
                    $key,
                    fn (Options $options, $value) =>
                        $this->resolve($value ?? [], $config['schema'])
                );
            } elseif (!empty($config['normalizer'])) {
                $resolver->setNormalizer($key, $config['normalizer']);
            }
        }

        return $resolver->resolve($data);
    }
}
