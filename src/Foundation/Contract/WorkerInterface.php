<?php

declare(strict_types=1);

/**
 * Derafu: aplicación PHP (Núcleo).
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

namespace Derafu\Lib\Core\Foundation\Contract;

use Derafu\Lib\Core\Common\Contract\OptionsAwareInterface;

/**
 * Interfaz para los workers de la aplicación.
 */
interface WorkerInterface extends ServiceInterface, OptionsAwareInterface
{
    /**
     * Entrega una estrategia específica que el worker implementa.
     *
     * @param string $strategy
     * @return StrategyInterface
     */
    public function getStrategy(string $strategy): StrategyInterface;

    /**
     * Entrega el listado de estrategias disponibles en el worker.
     *
     * @return StrategyInterface[]
     */
    public function getStrategies(): array;
}
