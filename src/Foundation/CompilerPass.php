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

namespace Derafu\Lib\Core\Foundation;

use Derafu\Lib\Core\Foundation\Contract\ServiceInterface;
use Derafu\Lib\Core\Helper\Str;
use LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Clase para modificar servicios durante la compilación.
 */
class CompilerPass implements CompilerPassInterface
{
    /**
     * Prefijo con el que deben ser nombrados todos los servicios asociados a la
     * aplicación.
     *
     * Esto se utiliza especialmente al nombrar paquetes, componentes y workers.
     *
     * @var string
     */
    protected string $servicesPrefix;

    /**
     * Patrones de búsqueda de clases de servicios de Foundation.
     *
     * Estas clases deben implementar además la interfaz ServiceInterface.
     *
     * @var array<string, string>
     */
    protected array $servicesPatterns = [
        // Strategies.
        'strategy' => "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Worker\\\\([A-Za-z0-9_]+)\\\\([A-Za-z0-9_]+)\\\\([A-Za-z0-9_]+)Strategy$/",
        // Workers.
        'worker' => "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\([A-Za-z0-9]+)WorkerInterface$/",
        // Components.
        'component' => "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\(?:[A-Z][a-zA-Z0-9]+)ComponentInterface$/",
        // Packages.
        'package' => "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Contract\\\\(?:[A-Z][a-zA-Z0-9]+)PackageInterface$/",
    ];

    /**
     * Constructor de la clase.
     *
     * @param string $servicesPrefix
     */
    public function __construct(string $servicesPrefix)
    {
        $this->servicesPrefix = $servicesPrefix;
    }

    /**
     * Procesar servicios en tiempo de compilación.
     *
     * @param ContainerBuilder $container
     * @return void
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            // Omitir servicios sintéticos y abstractos.
            //   - Sintéticos: si el contenedor no lo crea ni lo gestiona.
            //   - Abstractos: plantilla que otros servicios heredan.
            // Solo se procesarán servicios reales (ni sintéticos ni abstractos)
            // y que son gestionados directamente por el contenedor.
            if ($definition->isSynthetic() || $definition->isAbstract()) {
                continue;
            }

            // Procesar paquetes, componentes y workers.
            $this->processFoundationService($id, $definition, $container);

            // Asignar los servicios como lazy.
            // Se creará un proxy y se cargará solo al acceder al servicio. Esto
            // es cuando se acceda a un método o propiedas (aunque no deberían
            // existir propiedades públicas en servicios). No se cargará el
            // servicio si solo se inyecta y guarda en el atributo de una clase.
            $definition->setLazy(true);
        }
    }

    /**
     * Procesa un servicio registrado en el contenedor.
     *
     * Este método permite realizar de manera automática:
     *
     *   - Crear alias para paquetes, componentes, workers y estrategias.
     *   - Agregar un tag a paquetes, componentes, workers y estrategias.
     *   - Marcar como servicio público los paquetes.
     *
     * @param string $id
     * @param Definition $definition
     * @param ContainerBuilder $container
     * @return void
     */
    private function processFoundationService(
        string $id,
        Definition $definition,
        ContainerBuilder $container
    ): void {
        // Solo se procesan servicios que implementen `ServiceInterface`.
        if (
            str_contains($id, '.')
            || !str_contains($id, '\\')
            || !in_array(ServiceInterface::class, (array) class_implements($id))
        ) {
            return;
        }

        // Revisar si la clase hace match con alguno de los patrones de búsqueda
        // de clases de servicios de Foundation.
        foreach ($this->servicesPatterns as $type => $regex) {
            if (preg_match($regex, $id, $matches)) {
                $package = Str::snake($matches[1]);
                $component = Str::snake($matches[2] ?? '');
                $worker = Str::snake($matches[3] ?? '');
                $strategyGroup = Str::snake($matches[4] ?? '');
                $strategy = Str::snake($matches[5] ?? '');
                if ($strategyGroup && $strategy) {
                    $strategy = $strategyGroup . '.' . $strategy;
                }

                match($type) {
                    'package' => $this->processServicePackage(
                        $id,
                        $definition,
                        $package,
                        $container
                    ),
                    'component' => $this->processServiceComponent(
                        $id,
                        $definition,
                        $package,
                        $component,
                        $container
                    ),
                    'worker' => $this->processServiceWorker(
                        $id,
                        $definition,
                        $package,
                        $component,
                        $worker,
                        $container
                    ),
                    'strategy' => $this->processServiceStrategy(
                        $id,
                        $definition,
                        $package,
                        $component,
                        $worker,
                        $strategy,
                        $container
                    ),
                    default => throw new LogicException(sprintf(
                        'Tipo de servicio %s no es manejado por CompilerPass::processFoundationService().',
                        $type
                    )),
                };
            }
        }
    }

    /**
     * Procesa un servicio que representa un paquete.
     *
     * @param string $serviceId
     * @param Definition $definition
     * @param string $package
     * @param ContainerBuilder $container
     * @return void
     */
    private function processServicePackage(
        string $serviceId,
        Definition $definition,
        string $package,
        ContainerBuilder $container
    ): void {
        $aliasId = $this->servicesPrefix . $package;
        $alias = $container->setAlias($aliasId, $serviceId);
        $alias->setPublic(true);

        $definition->addTag('package', [
            'name' => $package,
        ]);
    }

    /**
     * Procesa un servicio que representa un componente.
     *
     * @param string $serviceId
     * @param Definition $definition
     * @param string $package
     * @param string $component
     * @param ContainerBuilder $container
     * @return void
     */
    private function processServiceComponent(
        string $serviceId,
        Definition $definition,
        string $package,
        string $component,
        ContainerBuilder $container
    ): void {
        $aliasId = $this->servicesPrefix . $package . '.' . $component;
        $alias = $container->setAlias($aliasId, $serviceId);

        $definition->addTag($package . '.component', [
            'name' => $component,
        ]);
    }

    /**
     * Procesa un servicio que representa un worker.
     *
     * @param string $serviceId
     * @param Definition $definition
     * @param string $package
     * @param string $component
     * @param string $worker
     * @param ContainerBuilder $container
     * @return void
     */
    private function processServiceWorker(
        string $serviceId,
        Definition $definition,
        string $package,
        string $component,
        string $worker,
        ContainerBuilder $container
    ): void {
        $aliasId = $this->servicesPrefix . $package . '.' . $component . '.' . $worker;
        $alias = $container->setAlias($aliasId, $serviceId);

        $definition->addTag($package . '.' . $component . '.worker', [
            'name' => $worker,
        ]);
    }

    /**
     * Procesa un servicio que representa un worker.
     *
     * @param string $serviceId
     * @param Definition $definition
     * @param string $package
     * @param string $component
     * @param string $worker
     * @param string $strategy
     * @param ContainerBuilder $container
     * @return void
     */
    private function processServiceStrategy(
        string $serviceId,
        Definition $definition,
        string $package,
        string $component,
        string $worker,
        string $strategy,
        ContainerBuilder $container
    ): void {
        $aliasId = $this->servicesPrefix . $package . '.' . $component . '.' . $worker . '.' . $strategy;
        $alias = $container->setAlias($aliasId, $serviceId);

        $definition->addTag($package . '.' . $component . '.' . $worker . '.strategy', [
            'name' => $strategy,
        ]);
    }
}
