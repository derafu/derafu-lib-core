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

namespace Derafu\Lib\Core\Package\Prime\Component\Template\Entity;

use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\DataInterface;

/**
 * Entidad que reprenta el dato de una variable en una plantilla.
 *
 * Esta clase permitirá realizar un manejo sobre el dato y entregarlo de una
 * forma estandarizada para su uso en la plantilla.
 */
class Data implements DataInterface
{
    /**
     * Identificador del dato representado.
     *
     * @var string
     */
    private string $id;

    /**
     * Valor del dato.
     *
     * @var mixed
     */
    private mixed $value;

    /**
     * Representación formateada del dato.
     *
     * @var string|null
     */
    private ?string $formatted;

    /**
     * Constructor de la entidad.
     *
     * @param string $id
     * @param mixed $value
     * @param string|null $formatted
     */
    public function __construct(string $id, mixed $value, string $formatted = null)
    {
        $this->id = $id;
        $this->value = $value;
        $this->formatted = $formatted;
    }

    /**
     * Entrega el string formateado del valor de este dato.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->formatted;
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @inheritDoc
     */
    public function setFormatted(string $formatted): static
    {
        $this->formatted = $formatted;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFormatted(): string
    {
        return $this->formatted ?? (string) $this->value;
    }
}
