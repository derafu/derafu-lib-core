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
use Derafu\Lib\Core\Foundation\Certificate\Worker\Validator;
use Derafu\Lib\Core\Support\Util\AsymmetricKey;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Validator::class)]
#[CoversClass(Certificate::class)]
#[CoversClass(CertificateException::class)]
#[CoversClass(Faker::class)]
#[CoversClass(Loader::class)]
#[CoversClass(AsymmetricKey::class)]
class CertificateValidatorTest extends TestCase
{
    private Faker $faker;

    private Validator $validator;

    protected function setUp(): void
    {
        $loader = new Loader();
        $this->faker = new Faker($loader);
        $this->validator = new Validator();
    }

    public function testValidCertificate(): void
    {
        $certificate = $this->faker->create();
        $this->validator->validate($certificate);
        $this->assertTrue(true);
    }

    public function testInvalidCertificate(): void
    {
        $this->expectException(CertificateException::class);
        $this->faker->setSubject(serialNumber: '123');
        $certificate = $this->faker->create();
        $this->validator->validate($certificate);
    }
}
