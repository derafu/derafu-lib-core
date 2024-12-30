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
     * @var array<string, array>
     */
    protected array $servicesPatterns = [
        // Strategies.
        'strategy' => [
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\([A-Za-z0-9]+)StrategyInterface$/",
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Worker\\\\([A-Za-z0-9_]+)\\\\Strategy\\\\([A-Za-z0-9_]+)\\\\([A-Za-z0-9_]+)Strategy$/",
        ],
        // Handlers.
        'handler' => [
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\([A-Za-z0-9]+)HandlerInterface$/",
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Worker\\\\([A-Za-z0-9_]+)\\\\Handler\\\\([A-Za-z0-9_]+)Handler$/",
        ],
        // Jobs.
        'job' => [
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\([A-Za-z0-9]+)JobInterface$/",
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Worker\\\\([A-Za-z0-9_]+)\\\\Job\\\\([A-Za-z0-9_]+)Job$/",
        ],
        // Workers.
        'worker' => [
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\([A-Za-z0-9]+)WorkerInterface$/",
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Worker\\\\([A-Za-z0-9_]+)Worker$/",
        ],
        // Components.
        'component' => [
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Contract\\\\(?:[A-Z][a-zA-Z0-9]+)ComponentInterface$/",
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)Component$/",
        ],
        // Packages.
        'package' => [
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Contract\\\\(?:[A-Z][a-zA-Z0-9]+)PackageInterface$/",
            "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\(?:[A-Za-z0-9_]+)Package$/",
        ],
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
            $this->processFoundationServiceDefinition(
                $id,
                $definition,
                $container
            );

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
     *   - Crear alias para paquetes, componentes, workers, trabajos, handlers y
     *     estrategias.
     *   - Agregar un tag a paquetes, componentes, workers, trabajos, handlers y
     *     estrategias.
     *   - Marcar como servicio público los paquetes.
     *
     * @param string $id
     * @param Definition $definition
     * @param ContainerBuilder $container
     * @return void
     */
    private function processFoundationServiceDefinition(
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
        foreach ($this->servicesPatterns as $type => $patterns) {
            foreach ($patterns as $pattern) {
                if (preg_match($pattern, $id, $matches)) {
                    $package = Str::snake($matches[1]);
                    $component = Str::snake($matches[2] ?? '');
                    $worker = Str::snake($matches[3] ?? '');
                    $action1 = Str::snake($matches[4] ?? ''); // Job, Handler o Strategy.
                    $action2 = Str::snake($matches[5] ?? ''); // Usado solo para estrategias por ahora.
                    $action = ($action1 && $action2)
                        ? $action1 . '.' . $action2
                        : $action1
                    ;

                    $this->processFoundationServiceType(
                        $type,
                        $id,
                        $definition,
                        $container,
                        $package,
                        $component,
                        $worker,
                        $action
                    );
                }
            }
        }
    }

    /**
     * Procesa un servicio genérico basado en su tipo.
     *
     * @param string $type El tipo de servicio: package, component, worker, job,
     * handler, strategy.
     * @param string $serviceId
     * @param Definition $definition
     * @param ContainerBuilder $container
     * @param string $package
     * @param string|null $component
     * @param string|null $worker
     * @param string|null $action
     * @return void
     */
    private function processFoundationServiceType(
        string $type,
        string $serviceId,
        Definition $definition,
        ContainerBuilder $container,
        string $package,
        ?string $component = null,
        ?string $worker = null,
        ?string $action = null
    ): void {
        // Construir alias ID según el tipo.
        $aliasParts = [$package];
        if ($component) {
            $aliasParts[] = $component;
        }
        if ($worker) {
            $aliasParts[] = $worker;
        }
        if ($action) {
            $aliasParts[] = $action;
        }

        $aliasId = $this->servicesPrefix . implode('.', $aliasParts);
        $alias = $container->setAlias($aliasId, $serviceId);

        // Determinar el tag y atributos según el tipo.
        $tagName = match ($type) {
            'package' => 'package',
            'component' => "{$package}.component",
            'worker' => "{$package}.{$component}.worker",
            'job' => "{$package}.{$component}.{$worker}.job",
            'handler' => "{$package}.{$component}.{$worker}.handler",
            'strategy' => "{$package}.{$component}.{$worker}.strategy",
            default => throw new LogicException(sprintf(
                'Tipo de servicio %s no es manejado por CompilerPass::processFoundationServiceType().',
                $type
            )),
        };

        $tagAttributes = [
            'name' => match ($type) {
                'package' => $package,
                'component' => $component,
                'worker' => $worker,
                'job' => $action,
                'handler' => $action,
                'strategy' => $action,
                default => null,
            },
        ];

        // Agregar tag al servicio.
        $definition->addTag($tagName, $tagAttributes);

        // Si el tipo es 'package', hacemos el alias público.
        if ($type === 'package') {
            $alias->setPublic(true);
        }
    }
}
