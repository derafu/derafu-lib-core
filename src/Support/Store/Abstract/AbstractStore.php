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

use ArrayAccess;
use Derafu\Lib\Core\Helper\Selector;
use Derafu\Lib\Core\Support\Store\Contract\StoreInterface;

/**
 * Clase base para todos los almacenamientos.
 */
abstract class AbstractStore implements StoreInterface
{
    /**
     * Datos almacenados.
     *
     * @var array|ArrayAccess
     */
    protected array|ArrayAccess $data = [];

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function set(string $key, mixed $value): static
    {
        Selector::set($this->data, $key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Selector::get($this->data, $key, $default);
    }

    /**
     * {@inheritdoc}
     */
    public function has(string $key): bool
    {
        return Selector::has($this->data, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function clear(string $key = null): void
    {
        if ($key === null) {
            $this->data = [];
        } else {
            Selector::clear($this->data, $key);
        }
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
}
