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

namespace Derafu\Lib\Core\Package\Prime\Component\Entity\Entity;

use Derafu\Lib\Core\Package\Prime\Component\Entity\Contract\EntityInterface;
use LogicException;

/**
 * Clase genérica para el manejo de entidades de repositorios.
 *
 * Esta clase es útil cuando no se desea crear explícitamente cada clase de cada
 * entidad. Sin embargo, es desaconsejado su uso y se recomienda crear clases
 * para cada entidad que se requiera.
 */
class Entity implements EntityInterface
{
    /**
     * Atributos de la entidad.
     *
     * @var array
     */
    private array $attributes = [];

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return static::class;
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute(string $name, int|float|string|bool|null $value): static
    {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttribute(string $name): int|float|string|bool|null
    {
        if (!$this->hasAttribute($name)) {
            throw new LogicException(sprintf(
                'No existe el atributo %s en la entidad %s.',
                $name,
                static::class
            ));
        }

        return $this->attributes[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Método mágico para obtener los atributos como si estuviesen definidos.
     *
     * @param string $name
     * @return int|float|string|bool|null
     */
    public function __get(string $name): int|float|string|bool|null
    {
        return $this->getAttribute($name);
    }
}
