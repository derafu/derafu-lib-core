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

namespace Derafu\Lib\Core\Package\Prime\Component\Entity\Repository;

use Derafu\Lib\Core\Foundation\Contract\FactoryInterface;
use Derafu\Lib\Core\Helper\Factory;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Contract\RepositoryInterface;
use Derafu\Lib\Core\Support\Store\Repository as StoreRepository;
use LogicException;
use Symfony\Component\Yaml\Yaml;

/**
 * Clase para repositorios de entidades.
 *
 * Proporciona métodos estándar para acceder y buscar entidades desde una fuente
 * de datos.
 */
class Repository extends StoreRepository implements RepositoryInterface
{
    /**
     * {@inheritdoc}
     */
    protected string $entityClass;

    /**
     * Nombre por defecto del índice del nombre cuando los datos de la entidad
     * no están normalizados y se debe crear el atributo.
     *
     * @var string
     */
    protected string $normalizationName = 'name';

    /**
     * Instancia de la fábrica de las entidades del repositorio.
     *
     * Esta fábrica no es obligatoria, se puede pasar la clase directamente.
     *
     * Solo es necesaria la fábrica si se desea algún tipo de personalización.
     *
     * @var FactoryInterface|null
     */
    protected ?FactoryInterface $factory = null;

    /**
     * Constructor del repositorio.
     *
     * @param string $entityClass
     * @param string|string $source
     */
    public function __construct(
        string $entityClass,
        string|array $source,
        ?string $normalizationName = null,
        ?FactoryInterface $factory = null
    ) {
        $this->entityClass = $entityClass;
        $this->factory = $factory;

        if (!is_array($source)) {
            $source = $this->loadSource($source);
        }

        $source = $this->normalizeData($source, $normalizationName);

        parent::__construct($source);
    }

    /**
     * Carga el archivo de datos desde diferentes formatos de archivo.
     *
     * @param string $source
     * @return array
     */
    protected function loadSource(string $source): array
    {
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        switch ($extension) {
            case 'php':
                $data = require $source;
                break;
            case 'json':
                $data = json_decode(file_get_contents($source), true);
                break;
            case 'yaml':
                $data = Yaml::parseFile(file_get_contents($source));
                break;
            default:
                throw new LogicException(sprintf(
                    'Formato de archivo %s de %s no soportado para la carga de repositorios.',
                    $extension,
                    basename($source)
                ));
        }

        if (!is_array($data)) {
            throw new LogicException(sprintf(
                'Los datos del repositorio de %s usando el origen %s no ha podido ser convertido a un arreglo válido para ser cargado en el repositorio.',
                $this->entityClass,
                $source
            ));
        }

        return $data;
    }

    /**
     * Normaliza los datos en caso que sea un arreglo de valores y no un arreglo
     * de arreglos.
     *
     * @param array $data
     * @return array
     */
    protected function normalizeData(array $data, ?string $normalizationName): array
    {
        $normalizationName = $normalizationName ?? $this->normalizationName;

        return array_map(function ($entity) use ($normalizationName) {
            if (!is_array($entity)) {
                return [
                    $normalizationName => $entity,
                ];
            }
            return $entity;
        }, $data);
    }

    /**
     * Crea una entidad a partir de los datos.
     *
     * @param array $data Datos que se asignarán a la entidad.
     * @return object Instancia de la entidad con los datos cargados.
     */
    protected function createEntity(array $data): object
    {
        if (isset($this->factory)) {
            return $this->factory->create($data);
        }

        return Factory::create($data, $this->entityClass);
    }
}
