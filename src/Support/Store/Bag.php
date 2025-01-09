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

use Derafu\Lib\Core\Helper\Arr;
use Derafu\Lib\Core\Support\Store\Abstract\AbstractStore;
use Derafu\Lib\Core\Support\Store\Contract\BagInterface;

/**
 * Clase para contenedor simple de datos.
 */
class Bag extends AbstractStore implements BagInterface
{
    /**
     * Constructor del contenedor.
     *
     * @param array $data Datos iniciales
     */
    public function __construct(array $data = [])
    {
        $this->data = $this->createFrom($data);
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $data): static
    {
        $this->data = $this->createFrom($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $data): static
    {
        $this->data = $this->createFrom(
            Arr::mergeRecursiveDistinct($this->toArray(), $data)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove(string $key): static
    {
        if ($this->has($key)) {
            unset($this->data[$key]);
        }

        return $this;
    }
}
