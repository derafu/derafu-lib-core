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
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Clase base para la clase principal de la biblioteca.
 */
abstract class AbstractApplication implements ApplicationInterface
{
    /**
     * Archivo de configuración por defecto con los servicios de la biblioteca.
     *
     * @var string
     */
    protected string $servicesConfigFile = 'config/services.yaml';

    /**
     * Prefijo con el que deben ser nombrados todos los servicios asociados a la
     * biblioteca.
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
     * Contenedor de servicios de la biblioteca.
     *
     * @var ContainerBuilder
     */
    private ContainerBuilder $container;

    /**
     * Constructor de la biblioteca.
     *
     * Debe ser privado para respetar el patrón singleton.
     *
     * @param ?string $servicesConfigFile Archivo de configuración de servicios.
     */
    private function __construct(?string $servicesConfigFile)
    {
        $this->container = new ContainerBuilder();
        $this->loadServices($servicesConfigFile);
        $this->container->compile();
    }

    /**
     * {@inheritdoc}
     */
    public static function getInstance(?string $servicesConfigFile = null): self
    {
        if (!isset(self::$instance)) {
            $class = static::class;
            self::$instance = new $class($servicesConfigFile);
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
     * Carga los servicios de la biblioteca al contenedor de servicios.
     *
     * @param ?string $configFile Archivo de configuración de servicios.
     * @return void
     */
    private function loadServices(?string $configFile): void
    {
        if ($configFile === null) {
            $configFile = $this->servicesConfigFile;

            if ($configFile[0] !== '/') {
                $configFile = dirname(__DIR__, 3) . '/' . $configFile;
            }
        }

        $loader = new YamlFileLoader(
            $this->container,
            new FileLocator(dirname($configFile))
        );

        $loader->load(basename($configFile));
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
