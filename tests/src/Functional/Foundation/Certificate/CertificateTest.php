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

namespace Derafu\Lib\Tests\Functional\Foundation\Certificate;

use Derafu\Lib\Core\Foundation\Certificate\Entity\Certificate;
use Derafu\Lib\Core\Foundation\Certificate\Exception\CertificateException;
use Derafu\Lib\Core\Foundation\Certificate\Worker\Faker;
use Derafu\Lib\Core\Foundation\Certificate\Worker\Loader;
use Derafu\Lib\Core\Support\Util\AsymmetricKey;
use Derafu\Lib\Core\Support\Util\Str;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Faker::class)]
#[CoversClass(Certificate::class)]
#[CoversClass(CertificateException::class)]
#[CoversClass(Loader::class)]
#[CoversClass(AsymmetricKey::class)]
#[CoversClass(Str::class)]
class CertificateTest extends TestCase
{
    private Faker $faker;

    protected function setUp(): void
    {
        $loader = new Loader();
        $this->faker = new Faker($loader);
    }

    public function testCertificateDefaultData(): void
    {
        $certificate = $this->faker->create();
        $expected = [
            'getID' => '11222333-9',
            'getName' => 'Daniel',
            'getEmail' => 'daniel.bot@example.com',
            'isActive' => true,
            'getIssuer' => 'LibreDTE Autoridad Certificadora de Pruebas',
        ];
        $actual = [
            'getID' => $certificate->getID(),
            'getName' => $certificate->getName(),
            'getEmail' => $certificate->getEmail(),
            'isActive' => $certificate->isActive(),
            'getIssuer' => $certificate->getIssuer(),
        ];
        $this->assertSame($expected, $actual);
    }

    public function testCertificateCreationWithValidSerialNumber(): void
    {
        $this->faker->setSubject(serialNumber: '1-9');
        $certificate = $this->faker->create();
        $this->assertSame('1-9', $certificate->getID());
    }

    public function testCertificateCreationWithInvalidSerialNumber(): void
    {
        $this->faker->setSubject(serialNumber: '1-2');
        $certificate = $this->faker->create();
        $this->assertNotSame('1-9', $certificate->getID());
    }

    public function testCertificateCreationWithKSerialNumber(): void
    {
        $this->faker->setSubject(serialNumber: '10-k');
        $certificate = $this->faker->create();
        $this->assertSame('10-K', $certificate->getID());
    }

    public function testGetModulus(): void
    {
        $certificate = $this->faker->create();
        $modulus = $certificate->getModulus();

        $this->assertNotEmpty($modulus);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9\/+=\n]+$/', $modulus);
    }

    public function testGetExponent(): void
    {
        $certificate = $this->faker->create();
        $exponent = $certificate->getExponent();

        $this->assertNotEmpty($exponent);
        $this->assertMatchesRegularExpression('/^[A-Za-z0-9\/+=]+$/', $exponent);
    }

    public function testGetNameThrowsExceptionForInvalidCertificate(): void
    {
        $this->expectException(CertificateException::class);

        $this->faker->setSubject(CN: '');
        $certificate = $this->faker->create();
        $certificate->getName();
    }

    public function testGetEmailThrowsExceptionForInvalidCertificate(): void
    {
        $this->expectException(CertificateException::class);

        $this->faker->setSubject(emailAddress: '');
        $certificate = $this->faker->create();
        $certificate->getEmail();
    }

    public function testIsActiveForExpiredCertificate(): void
    {
        $certificate = $this->faker->create();

        $when = date('Y-m-d', strtotime('+10 year'));
        $this->assertFalse($certificate->isActive($when));
    }

    public function testGetExpirationDays(): void
    {
        $certificate = $this->faker->create();
        $days = $certificate->getExpirationDays();

        $this->assertGreaterThan(0, $days);
        $this->assertLessThanOrEqual(365, $days);
    }

    public function testGetDataReturnsParsedCertificateData(): void
    {
        $certificate = $this->faker->create();
        $data = $certificate->getData();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('subject', $data);
        $this->assertArrayHasKey('issuer', $data);
    }
}
