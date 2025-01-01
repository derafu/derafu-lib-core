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

namespace Derafu\Lib\Core\Package\Prime\Component\Entity\Worker;

use Derafu\Lib\Core\Foundation\Abstract\AbstractWorker;
use Derafu\Lib\Core\Foundation\Contract\FactoryInterface;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Contract\ManagerWorkerInterface;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Contract\RepositoryInterface;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Entity\Entity;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Exception\ManagerException;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Repository\Repository;

/**
 * Worker "prime.entity.manager".
 */
class ManagerWorker extends AbstractWorker implements ManagerWorkerInterface
{
    /**
     * Esquema de configuración del worker.
     *
     * @var array
     */
    protected array $configurationSchema = [
        'entity' => [
            'types' => 'array',
            'default' => [],
            'schema' => [
                'normalizationName' => [
                    'types' => 'string',
                    'default' => 'name',
                ],
            ],
        ],
    ];

    /**
     * Listado de fuentes de datos de repositorios de entidades.
     *
     * @var array
     */
    private array $sources;

    /**
     * Listado de repositorios que ya han sido cargados desde sus fuentes de
     * datos.
     *
     * @var array
     */
    private array $repositories = [];

    /**
     * Constructor del worker.
     *
     * @param array $sources
     */
    public function __construct(array $sources = [])
    {
        $this->sources = $sources;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(
        string $source,
        ?FactoryInterface $factory = null
    ): RepositoryInterface {
        // Si el repositorio no está cargado se trata de cargar.
        if (!isset($this->repositories[$source])) {
            // Si no hay fuente de datos para el repositorio se genera un error.
            if (!isset($this->sources[$source])) {
                throw new ManagerException(sprintf(
                    'No existe una fuente de datos configurada para crear un repositorio de %s.',
                    $source
                ));
            }

            // Se tratará de crear el repositorio a partir de la fuente de datos
            // asignada.
            $this->repositories[$source] = new Repository(
                $this->resolveEntityClass($source),
                $this->sources[$source],
                $this->getConfiguration()->get('entity.normalizationName'),
                $factory
            );
        }

        // Retornar el repositorio solicitado.
        return $this->repositories[$source];
    }

    /**
     * Determina la entidad que se debe utilizar a partir del ID del origen del
     * repositorio.
     *
     * @param string $source
     * @return string
     */
    private function resolveEntityClass(string $source): string
    {
        if (class_exists($source)) {
            return $source;
        }

        return Entity::class;
    }
}
