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

namespace Derafu\Lib\Core\Foundation\Abstract;

use Derafu\Lib\Core\Foundation\Contract\ComponentInterface;
use Derafu\Lib\Core\Foundation\Contract\PackageInterface;
use LogicException;

/**
 * Clase base para los paquetes de la aplicación.
 */
abstract class AbstractPackage extends AbstractService implements PackageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getComponent(string $component): ComponentInterface
    {
        // Obtener todos los componentes del paquete.
        $components = $this->getComponents();

        // No se encontró el componente.
        if (!isset($components[$component])) {
            throw new LogicException(sprintf(
                'El componente %s no existe en el paquete.',
                $component
            ));
        }

        // Entregar el componente encontrado.
        return $components[$component];
    }
}
