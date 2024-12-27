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
 * Debería haber recibido una copia de la Licencia Pública General Affero de
 * GNU junto a este programa.
 *
 * En caso contrario, consulte <http://www.gnu.org/licenses/agpl.html>.
 */

namespace Derafu\Lib\Tests\Functional;

use Derafu\Lib\Core\Application;
use Derafu\Lib\Core\Common\Abstract\AbstractServiceRegistry;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use stdClass;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[CoversClass(Application::class)]
#[CoversClass(AbstractServiceRegistry::class)]
class ApplicationTest extends TestCase
{
    public function testApplicationLoadTestServices(): void
    {
        // Cargar la configuración específica para pruebas.
        $app = Application::getInstance(TestServiceRegistry::class);

        // Verificar que los servicios del archivo de pruebas pueden resolverse.
        $service = $app->getService('example_service');
        $this->assertInstanceOf(stdClass::class, $service);
    }

    public function testApplicationServiceNotFound(): void
    {
        // Cargar la configuración específica para pruebas.
        $app = Application::getInstance(TestServiceRegistry::class);

        // Intentar resolver un servicio no definido.
        $this->expectException(ServiceNotFoundException::class);
        $app->getService('non_existent_service');
    }

    public function testApplicationGlobalFunction(): void
    {
        // Cargar la configuración específica para pruebas.
        $app = derafu_lib(TestServiceRegistry::class);

        // Verificar que los servicios del archivo de pruebas pueden resolverse.
        $service = $app->getService('example_service');
        $this->assertInstanceOf(stdClass::class, $service);
    }
}

final class TestServiceRegistry extends AbstractServiceRegistry
{
    protected string $configPath = __DIR__ . '/../../fixtures/application';
}
