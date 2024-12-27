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

namespace Derafu\Lib\Tests\Integration;

use Derafu\Lib\Core\Derafu;
use Derafu\Lib\Core\Foundation\Certificate\CertificateService;
use Derafu\Lib\Core\Foundation\Certificate\Contract\CertificateServiceInterface;
use Derafu\Lib\Core\Foundation\Log\Contract\LogServiceInterface;
use Derafu\Lib\Core\Foundation\Log\LogService;
use Derafu\Lib\Core\Foundation\Log\Worker\StorageHandler as LogStorageHandler;
use Derafu\Lib\Core\Foundation\Signature\Contract\SignatureServiceInterface;
use Derafu\Lib\Core\Foundation\Signature\SignatureService;
use Derafu\Lib\Core\Foundation\Signature\Worker\Generator as SignatureGenerator;
use Derafu\Lib\Core\Foundation\Signature\Worker\Validator as SignatureValidator;
use Derafu\Lib\Core\Foundation\Xml\Contract\XmlServiceInterface;
use Derafu\Lib\Core\Foundation\Xml\XmlService;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

#[CoversClass(Derafu::class)]
#[CoversClass(CertificateService::class)]
#[CoversClass(LogService::class)]
#[CoversClass(LogStorageHandler::class)]
#[CoversClass(SignatureService::class)]
#[CoversClass(SignatureGenerator::class)]
#[CoversClass(SignatureValidator::class)]
#[CoversClass(XmlService::class)]
class DerafuTest extends TestCase
{
    private array $testCases = [
        'services' => [
            'certificate' => CertificateServiceInterface::class,
            'log' => LogServiceInterface::class,
            'signature' => SignatureServiceInterface::class,
            'xml' => XmlServiceInterface::class,
        ],
    ];

    public function testDerafuFunction(): void
    {
        $derafu = derafu_lib();

        $this->assertInstanceOf(Derafu::class, $derafu);
    }

    public function testDerafuGetServices(): void
    {
        $derafu = derafu_lib();

        foreach ($this->testCases['services'] as $name => $interface) {
            $this->assertInstanceOf($interface, $derafu->getService($name));
        }
    }

    public function testDerafuServiceNotFoundWrongServiceName(): void
    {
        $derafu = derafu_lib();

        $this->expectException(ServiceNotFoundException::class);

        $derafu->getService('certificat');
    }

    public function testDerafuServiceNotFoundInterfaceServiceName(): void
    {
        $derafu = derafu_lib();

        $this->expectException(ServiceNotFoundException::class);

        $derafu->getService(CertificateServiceInterface::class);
    }
}
