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

namespace Derafu\Lib\Core\Package\Prime\Component\Signature\Contract;

use Derafu\Lib\Core\Foundation\Contract\ComponentInterface;

/**
 * Interfaz para la clase del servicio de firma electrónica.
 */
interface SignatureComponentInterface extends ComponentInterface
{
    /**
     * Entrega el worker "prime.signature.generator".
     *
     * @return GeneratorWorkerInterface
     */
    public function getGeneratorWorker(): GeneratorWorkerInterface;

    /**
     * Entrega el worker "prime.signature.validator".
     *
     * @return ValidatorWorkerInterface
     */
    public function getValidatorWorker(): ValidatorWorkerInterface;
}