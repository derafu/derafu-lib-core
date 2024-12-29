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
use Derafu\Lib\Core\Support\Store\Contract\RepositoryInterface;

/**
* Clase para repositorios de objetos/entidades.
*
* Proporciona métodos estándar para acceder y buscar objetos/entidades desde
* una fuente de datos.
*/
class Repository extends AbstractStore implements RepositoryInterface
{
    /**
     * Constructor del repositorio.
     *
     * @param array|string $source Arreglo de datos o ruta al archivo PHP.
     */
    public function __construct(array|string $source)
    {
        $data = is_string($source) ? require $source : $source;

        $this->data = array_combine(
            array_keys($data),
            array_map(
                fn ($id, $item) => array_merge(['id' => $id], $item),
                array_keys($data),
                array_values($data)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function find($id, $lockMode = null, $lockVersion = null): ?object
    {
        return isset($this->data[$id])
            ? $this->createEntity($this->data[$id])
            : null
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return array_values(array_map(
            fn ($item) => $this->createEntity($item),
            $this->data
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function findBy(
        array $criteria,
        ?array $orderBy = null,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $results = array_filter(
            $this->data,
            fn ($item) => $this->matchCriteria($item, $criteria)
        );

        if ($orderBy) {
            $results = $this->applyOrderBy($results, $orderBy);
        }

        if ($offset !== null || $limit !== null) {
            $results = array_slice($results, $offset ?: 0, $limit);
        }

        return array_values(array_map(
            fn ($item) => $this->createEntity($item),
            $results
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function findOneBy(array $criteria, ?array $orderBy = null): ?object
    {
        $results = $this->findBy($criteria, $orderBy, 1);

        return empty($results) ? null : reset($results);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->data);
    }

    /**
     * Verifica si un item cumple con los criterios de búsqueda.
     */
    protected function matchCriteria(array $item, array $criteria): bool
    {
        foreach ($criteria as $field => $value) {
            if (!isset($item[$field]) || $item[$field] !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Ordena los resultados según los criterios especificados.
     *
     * @param array $results Resultados a ordenar.
     * @param array $orderBy Criterios de ordenamiento ['campo' => 'ASC|DESC'].
     * @return array Resultados ordenados.
     */
    protected function applyOrderBy(array $results, array $orderBy): array
    {
        uasort($results, function ($a, $b) use ($orderBy) {
            foreach ($orderBy as $field => $direction) {
                if (!isset($a[$field]) || !isset($b[$field])) {
                    continue;
                }

                $compare = $direction === 'DESC'
                    ? -1 * ($a[$field] <=> $b[$field])
                    : $a[$field] <=> $b[$field];

                if ($compare !== 0) {
                    return $compare;
                }
            }
            return 0;
        });

        return $results;
    }

    /**
     * Crea una entidad a partir de los datos.
     *
     * Por defecto retorna un objeto stdClass, las clases hijas pueden
     * sobreescribir este método para retornar una entidad específica.
     */
    protected function createEntity(array $data): object
    {
        return (object) $data;
    }
}
