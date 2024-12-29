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

namespace Derafu\Lib\Tests\Functional\Foundation;

use Derafu\Lib\Core\Foundation\Abstract\AbstractService;
use Derafu\Lib\Core\Foundation\Abstract\AbstractServiceRegistry;
use Derafu\Lib\Core\Foundation\Adapter\ServiceAdapter;
use Derafu\Lib\Core\Foundation\Application;
use Derafu\Lib\Core\Foundation\CompilerPass;
use Derafu\Lib\Core\Foundation\Contract\ServiceInterface;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[CoversClass(AbstractServiceRegistry::class)]
#[CoversClass(Application::class)]
#[CoversClass(CompilerPass::class)]
#[CoversClass(ServiceAdapter::class)]
class ApplicationTest extends TestCase
{
    private Application $app;

    // Cargar la configuración específica para pruebas.
    protected function setUp(): void
    {
        $this->app = Application::getInstance(TestServiceRegistry::class);
    }

    // Obtener un servicio público.
    public function testApplicationGetServicePublic(): void
    {
        $service = $this->app->getService('public_service');
        $this->assertInstanceOf(TestService::class, $service);
    }

    // Intentar obtener un servicio privado.
    public function testApplicationGetServicePrivate(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->app->getService('non_existent_service');
    }

    // Intentar obtener un servicio no definido.
    public function testApplicationGetServiceNotFound(): void
    {
        $this->expectException(ServiceNotFoundException::class);
        $this->app->getService('non_existent_service');
    }

    // Obtener servicio que requiere un adaptador.
    public function testApplicationGetServiceStdClass(): void
    {
        $service = $this->app->getService('stdClass_service');
        $this->assertInstanceOf(ServiceInterface::class, $service);
    }

    // Obtener servicio que requiere un adaptador y llamar a un método.
    public function testApplicationGetServiceAdaptee(): void
    {
        $service = $this->app->getService('adaptee_service');

        $this->assertInstanceOf(ServiceAdapter::class, $service);

        if ($service instanceof ServiceAdapter) {
            /** @phpstan-ignore-next-line */
            $this->assertSame(123, $service->getId());
        }
    }

    // Obtener servicio a través de la función global.
    public function testApplicationGlobalFunction(): void
    {
        $app = derafu_lib(TestServiceRegistry::class);

        $service = $app->getService('public_service');
        $this->assertInstanceOf(TestService::class, $service);
    }
}

class TestServiceRegistry extends AbstractServiceRegistry
{
    protected string $configPath = __DIR__ . '/../../../fixtures/application';
}

class TestService extends AbstractService
{
}

class TestAdaptee
{
    public function getId(): int
    {
        return 123;
    }
}
