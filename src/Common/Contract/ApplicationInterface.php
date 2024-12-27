<?php

declare(strict_types=1);

/**
 * Derafu: aplicación PHP (Núcleo).
 * Copyright (C) Derafu <https://www.derafu.org>
 *
 * Este programa es software libre: usted puede redistribuirlo y/o modificarlo
 * bajo los términos de la Licencia Pública General Affero de GNU publicada
 * por la Fundación para el Software Libre, ya sea la versión 3 de la Licencia,
 * o (a su elección) cualquier versión posterior de la misma.
 *
 * Este programa se distribuye con la esperanza de que sea útil, pero SIN
 * GARANTÍA ALGUNA; ni siquiera la garantía implícita MERCANTIL o de APTITUD
 * PARA UN PROPÓSITO DETERMINADO. Consulte los detalles de la Licencia Pública
 * General Affero de GNU para obtener una información más detallada.
 *
 * Debería haber recibido una copia de la Licencia Pública General Affero de
 * GNU junto a este programa.
 *
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

namespace Derafu\Lib\Core\Common\Contract;

/**
 * Interfaz para la clase principal de la aplicación.
 */
interface ApplicationInterface
{
    /**
     * Singleton para obtener siempre la misma instancia.
     *
     * @param ?string $servicesConfigFile Archivo de configuración de servicios.
     * @return self
     */
    public static function getInstance(?string $servicesConfigFile = null): self;

    /**
     * Obtiene un servicio registrado en el contenedor.
     *
     * @param string $service
     * @return object
     */
    public function getService(string $service): object;

    /**
     * Verifica si un servicio está registrado.
     *
     * @param string $service
     * @return boolean
     */
    public function hasService(string $service): bool;
}
