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

namespace Derafu\Lib\Core\Package\Prime\Component\Template\Contract;

use Derafu\Lib\Core\Common\Contract\StringableInterface;

/**
 * Interfaz para la clase que representa un dato en una plantilla.
 */
interface DataInterface extends StringableInterface
{
    /**
     * Obtiene el identificador del dato representado.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Obtiene el valor del dato.
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * Asigna la representación formateada del valor.
     *
     * @param string $formatted
     * @return static
     */
    public function setFormatted(string $formatted): static;

    /**
     * Obtiene la representación formateada del valor.
     *
     * @return string
     */
    public function getFormatted(): string;
}
