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

namespace Derafu\Lib\Core\Foundation\Signature;

use Derafu\Lib\Core\Foundation\Certificate\Entity\Certificate;
use Derafu\Lib\Core\Foundation\Signature\Contract\GeneratorInterface;
use Derafu\Lib\Core\Foundation\Signature\Contract\SignatureServiceInterface;
use Derafu\Lib\Core\Foundation\Signature\Contract\ValidatorInterface;
use Derafu\Lib\Core\Foundation\Xml\Entity\Xml;

/**
 * Servicio de firma electrónica.
 */
class SignatureService implements SignatureServiceInterface
{
    /**
     * Generador de firmas electrónicas.
     *
     * @var GeneratorInterface
     */
    private GeneratorInterface $generator;

    /**
     * Validador de firmas electrónicas.
     *
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * Constructor del servicio de firma electrónica.
     *
     * @param GeneratorInterface $generator
     * @param ValidatorInterface $validator
     */
    public function __construct(
        GeneratorInterface $generator,
        ValidatorInterface $validator
    ) {
        $this->generator = $generator;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function sign(
        string $data,
        string $privateKey,
        string|int $signatureAlgorithm = OPENSSL_ALGO_SHA1
    ): string {
        return $this->generator->sign(
            $data,
            $privateKey,
            $signatureAlgorithm
        );
    }

    /**
     * {@inheritdoc}
     */
    public function signXml(
        Xml|string $xml,
        Certificate $certificate,
        ?string $reference = null
    ): string {
        return $this->generator->signXml(
            $xml,
            $certificate,
            $reference
        );
    }

    /**
     * {@inheritdoc}
     */
    public function digestXmlReference(
        Xml $doc,
        ?string $reference = null
    ): string {
        return $this->generator->digestXmlReference(
            $doc,
            $reference
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validate(
        string $data,
        string $signature,
        string $publicKey,
        string|int $signatureAlgorithm = OPENSSL_ALGO_SHA1
    ): bool {
        return $this->validator->validate(
            $data,
            $signature,
            $publicKey,
            $signatureAlgorithm
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateXml(Xml|string $xml): void
    {
        $this->validator->validateXml($xml);
    }
}
