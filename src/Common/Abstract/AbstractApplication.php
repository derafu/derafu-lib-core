<?php

declare(strict_types=1);

/**
 * Derafu: Biblioteca PHP (Núcleo).
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

namespace Derafu\Lib\Core\Common\Abstract;

use Derafu\Lib\Core\Common\Contract\ApplicationInterface;
use Derafu\Lib\Core\Common\Contract\ServiceRegistryInterface;
use Derafu\Lib\Core\ServiceRegistry;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Clase base para la clase principal de la aplicación.
 */
abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * Clase para el registro de los servicios de la aplicación.
     */
    protected string $serviceRegistry = ServiceRegistry::class;

    /**
     * Prefijo con el que deben ser nombrados todos los servicios asociados a la
     * aplicación.
     *
     * @var string
     */
    protected string $servicesPrefix = 'derafu.lib.';

    /**
     * Instancia de la clase para el patrón singleton.
     *
     * @var self
     */
    private static self $instance;

    /**
     * Contenedor de servicios de la aplicación.
     *
     * @var ContainerBuilder
     */
    private ContainerBuilder $container;

    /**
     * Constructor de la aplicación.
     *
     * Debe ser privado para respetar el patrón singleton.
     */
    private function __construct(?string $serviceRegistry)
    {
        $this->container = new ContainerBuilder();
        $this->getServiceRegistry($serviceRegistry)->register($this->container);
        $this->container->compile();
    }

    /**
     * {@inheritdoc}
     */
    public static function getInstance(?string $serviceRegistry = null): self
    {
        if (!isset(self::$instance)) {
            $class = static::class;
            self::$instance = new $class($serviceRegistry);
        }

        return self::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getService(string $service): object
    {
        $service = $this->normalizeServiceName($service);

        return $this->container->get($service);
    }

    /**
     * {@inheritdoc}
     */
    public function hasService(string $service): bool
    {
        $service = $this->normalizeServiceName($service);

        return $this->container->has($service);
    }

    /**
     * Obtiene el registry de servicios.
     */
    private function getServiceRegistry(
        ?string $class = null
    ): ServiceRegistryInterface {
        $class = $class ?? $this->serviceRegistry;
        $serviceRegistry = new $class();

        return $serviceRegistry;
    }

    /**
     * Normaliza el nombre del servicio.
     *
     * Solo se normaliza si el nombre es el código del servicio y no tiene el
     * prefijo requerido. Si se solicita el servicio a través del nombre de una
     * clase (FQCN) no se normalizará.
     *
     * @param string $service
     * @return string
     */
    private function normalizeServiceName(string $service): string
    {
        if (str_contains($service, '\\')) {
            return $service;
        }

        if (str_starts_with($service, $this->servicesPrefix)) {
            return $service;
        }

        return $this->servicesPrefix . $service;
    }
}
