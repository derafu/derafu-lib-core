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

use Derafu\Lib\Core\Foundation\Adapter\ServiceAdapter;
use Derafu\Lib\Core\Foundation\CompilerPass;
use Derafu\Lib\Core\Foundation\Contract\ApplicationInterface;
use Derafu\Lib\Core\Foundation\Contract\PackageInterface;
use Derafu\Lib\Core\Foundation\Contract\ServiceInterface;
use Derafu\Lib\Core\Foundation\Contract\ServiceRegistryInterface;
use Derafu\Lib\Core\Foundation\ServiceRegistry;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

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
     * Esto se utiliza especialmente al nombrar paquetes.
     *
     * @var string
     */
    protected string $servicesPrefix = 'derafu.lib.';

    /**
     * Indica si se deben procesar automáticamente los servicios después de
     * haber sido registrados durante el tiempo de compilación.
     *
     * Esto permite autoconfigurar opciones que no se asignaron en services.yaml
     * o ejecutar cualquier otra lógica una vez que el servicio ya fue
     * registrado en el contenedor de servicios.
     *
     * @var boolean
     */
    protected bool $processServices = true;

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

        if ($this->processServices) {
            $this->container->addCompilerPass(
                new CompilerPass($this->servicesPrefix)
            );
        }

        $this->container->compile();
    }

    /**
     * {@inheritdoc}
     */
    public static function getInstance(?string $serviceRegistry = null): static
    {
        if (!isset(self::$instance)) {
            $class = static::class;
            self::$instance = new $class($serviceRegistry);
        }

        assert(self::$instance instanceof static);

        return self::$instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackage(string $package): PackageInterface
    {
        $service = $this->servicesPrefix . $package;

        try {
            $instance = $this->container->get($service);
        } catch (ServiceNotFoundException $e) {
            $instance = null;
        }

        if (!$instance instanceof PackageInterface) {
            throw new LogicException(sprintf(
                'El paquete %s no existe en la aplicación.',
                $package
            ));
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages(): array
    {
        $packages = [];
        foreach ($this->container->findTaggedServiceIds('package') as $id => $tags) {
            $packages[str_replace($this->servicesPrefix, '', $id)] =
                $this->container->get($id)
            ;
        }

        return $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function getService(string $service): ServiceInterface
    {
        $service = $this->normalizeServiceName($service);
        $instance = $this->container->get($service);

        if ($instance instanceof ServiceInterface) {
            return $instance;
        }

        return new ServiceAdapter($instance);
    }

    /**
     * {@inheritdoc}
     */
    public function hasPackage(string $package): bool
    {
        $service = $this->servicesPrefix . $package;

        return $this->container->has($service);
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
