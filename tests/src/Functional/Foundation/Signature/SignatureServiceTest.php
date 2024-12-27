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

namespace Derafu\Lib\Tests\Functional\Foundation\Signature;

use Derafu\Lib\Core\Foundation\Certificate\Entity\Certificate;
use Derafu\Lib\Core\Foundation\Certificate\Exception\CertificateException;
use Derafu\Lib\Core\Foundation\Certificate\Worker\Faker as CertificateFaker;
use Derafu\Lib\Core\Foundation\Certificate\Worker\Loader as CertificateLoader;
use Derafu\Lib\Core\Foundation\Signature\Entity\XmlSignatureNode;
use Derafu\Lib\Core\Foundation\Signature\Exception\SignatureException;
use Derafu\Lib\Core\Foundation\Signature\SignatureService;
use Derafu\Lib\Core\Foundation\Signature\Worker\Generator;
use Derafu\Lib\Core\Foundation\Signature\Worker\Validator;
use Derafu\Lib\Core\Foundation\Xml\Entity\Xml;
use Derafu\Lib\Core\Foundation\Xml\Exception\XmlException;
use Derafu\Lib\Core\Foundation\Xml\Worker\Decoder as XmlDecoder;
use Derafu\Lib\Core\Foundation\Xml\Worker\Encoder as XmlEncoder;
use Derafu\Lib\Core\Foundation\Xml\Worker\Validator as XmlValidator;
use Derafu\Lib\Core\Foundation\Xml\XmlService;
use Derafu\Lib\Core\Support\Util\AsymmetricKey;
use Derafu\Lib\Core\Support\Util\Str;
use Derafu\Lib\Core\Support\Util\Xml as XmlUtil;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Certificate::class)]
#[CoversClass(CertificateException::class)]
#[CoversClass(CertificateFaker::class)]
#[CoversClass(CertificateLoader::class)]
#[CoversClass(XmlSignatureNode::class)]
#[CoversClass(SignatureService::class)]
#[CoversClass(Generator::class)]
#[CoversClass(Validator::class)]
#[CoversClass(Xml::class)]
#[CoversClass(XmlException::class)]
#[CoversClass(XmlDecoder::class)]
#[CoversClass(XmlEncoder::class)]
#[CoversClass(XmlValidator::class)]
#[CoversClass(XmlService::class)]
#[CoversClass(AsymmetricKey::class)]
#[CoversClass(Str::class)]
#[CoversClass(XmlUtil::class)]
class SignatureServiceTest extends TestCase
{
    private string $xmlDir;

    private SignatureService $signatureService;

    private Certificate $certificate;

    protected function setUp(): void
    {
        $this->xmlDir = self::getFixturesPath('foundation/signature');

        $xmlEncoder = new XmlEncoder();
        $xmlDecoder = new XmlDecoder();
        $xmlValidator = new XmlValidator();
        $xmlService = new XmlService($xmlEncoder, $xmlDecoder, $xmlValidator);

        $generator = new Generator($xmlService);
        $validator = new Validator($generator, $xmlService);
        $this->signatureService = new SignatureService($generator, $validator);

        $certificateLoader = new CertificateLoader();
        $certficateFaker = new CertificateFaker($certificateLoader);
        $this->certificate = $certficateFaker->create();
    }

    public function testSignatureServiceSignXmlString(): void
    {
        $xmlUnsigned = file_get_contents($this->xmlDir . '/unsigned.xml');
        $xmlSigned = $this->signatureService->signXml(
            $xmlUnsigned,
            $this->certificate
        );

        $this->signatureService->validateXml($xmlSigned);
        $this->assertTrue(true);
    }

    public function testSignatureServiceSignXmlObject(): void
    {
        $xmlUnsigned = file_get_contents($this->xmlDir . '/unsigned.xml');
        $xml = new Xml();
        $xml->loadXml($xmlUnsigned);
        $xmlSigned = $this->signatureService->signXml(
            $xml,
            $this->certificate
        );

        $this->signatureService->validateXml($xmlSigned);
        $this->assertTrue(true);
    }

    public function testSignatureServiceSignXmlWithReference(): void
    {
        $xmlUnsigned = file_get_contents($this->xmlDir . '/unsigned.xml');
        $xml = new Xml();
        $xml->loadXml($xmlUnsigned);
        $xmlSigned = $this->signatureService->signXml(
            $xml,
            $this->certificate,
            'LibreDTE_SetDoc'
        );

        $this->signatureService->validateXml($xmlSigned);
        $this->assertTrue(true);
    }

    public function testSignatureServiceSignXmlWithInvalidReference(): void
    {
        $this->expectException(XmlException::class);

        $xmlUnsigned = file_get_contents($this->xmlDir . '/unsigned.xml');
        $xml = new Xml();
        $xml->loadXml($xmlUnsigned);
        $xmlSigned = $this->signatureService->signXml(
            $xml,
            $this->certificate,
            'LibreDTE_SetDo'
        );
    }

    public function testSignatureServiceValidXmlSignature(): void
    {
        $xml = file_get_contents($this->xmlDir . '/valid_signed.xml');
        $this->signatureService->validateXml($xml);
        $this->assertTrue(true);
    }

    public function testSignatureServiceInvalidXmlSignature(): void
    {
        $this->expectException(SignatureException::class);

        $xml = file_get_contents($this->xmlDir . '/invalid_signed.xml');
        $this->signatureService->validateXml($xml);
    }
}
