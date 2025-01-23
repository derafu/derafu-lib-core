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

namespace Derafu\Lib\Core\Package\Prime\Component\Mail\Contract;

use Derafu\Lib\Core\Support\Store\Contract\DataContainerInterface;

/**
 * Interfaz para el "cartero" que será usado para el transporte al enviar o
 * recibir los correos electrónicos.
 */
interface PostmanInterface
{
    /**
     * Agregar un sobre al cartero.
     *
     * @param EnvelopeInterface $envelope
     * @return static
     */
    public function addEnvelope(EnvelopeInterface $envelope): static;

    /**
     * Obtiene el listado de sobres que el cartero tiene.
     *
     * @return EnvelopeInterface[]
     */
    public function getEnvelopes(): array;

    /**
     * Asigna las opciones para que el cartero transporte los sobres.
     *
     * @param DataContainerInterface|array $options
     * @return static
     */
    public function setOptions(DataContainerInterface|array $options): static;

    /**
     * Obtiene las opciones que se deben usar para transportar los sobres.
     *
     * @return DataContainerInterface
     */
    public function getOptions(): DataContainerInterface;
}
