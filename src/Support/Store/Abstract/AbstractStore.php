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

namespace Derafu\Lib\Core\Support\Store\Abstract;

use Derafu\Lib\Core\Helper\Selector;
use Derafu\Lib\Core\Support\Store\Contract\StoreInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;

/**
 * Clase base para todos los almacenamientos.
 */
abstract class AbstractStore implements StoreInterface
{
    /**
     * Colección de datos almacenados.
     *
     * @var ArrayCollection
     */
    protected ArrayCollection $data;

    /**
     * {@inheritdoc}
     */
    public function collection(): ArrayCollection
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, mixed $value): static
    {
        $data = $this->toArray();
        Selector::set($data, $key, $value);
        $this->data = $this->createFrom($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Selector::get($this->toArray(), $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return Selector::has($this->toArray(), $key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(string $key = null): void
    {
        if ($key === null) {
            $this->data = $this->createFrom([]);
        } else {
            $data = $this->toArray();
            Selector::clear($data, $key);
            $this->data = $this->createFrom($data);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function matching(Criteria $criteria): ArrayCollection
    {
        return $this->createFrom($this->data->matching($criteria)->toArray());
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set((string) $offset, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get((string) $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has((string) $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->clear((string) $offset);
    }

    /**
     * Crea una nueva instancia del tipo de datos que utiliza la propieda $data.
     *
     * @param array $array
     * @return ArrayCollection
     */
    protected function createFrom(array $array): ArrayCollection
    {
        return new ArrayCollection($array);
    }

    /**
     * Entrega los elementos de $data como arreglo.
     *
     * @return array
     */
    protected function toArray(): array
    {
        return $this->data->toArray();
    }
}
