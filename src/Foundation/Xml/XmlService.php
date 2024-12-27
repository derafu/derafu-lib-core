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

namespace Derafu\Lib\Core\Foundation\Xml;

use Derafu\Lib\Core\Foundation\Xml\Contract\DecoderInterface;
use Derafu\Lib\Core\Foundation\Xml\Contract\EncoderInterface;
use Derafu\Lib\Core\Foundation\Xml\Contract\ValidatorInterface;
use Derafu\Lib\Core\Foundation\Xml\Contract\XmlServiceInterface;
use Derafu\Lib\Core\Foundation\Xml\Entity\Xml;
use DOMElement;

/**
 * Servicio para trabajar con documentos XML.
 */
class XmlService implements XmlServiceInterface
{
    /**
     * Codificador de arreglo PHP a XML.
     *
     * @var EncoderInterface
     */
    private EncoderInterface $encoder;

    /**
     * Decodificador de XML a arreglo PHP.
     *
     * @var DecoderInterface
     */
    private DecoderInterface $decoder;

    /**
     * Validador de documentos XML.
     *
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * Constructor del servicio.
     *
     * @param EncoderInterface $encoder
     * @param DecoderInterface $decoder
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EncoderInterface $encoder,
        DecoderInterface $decoder,
        ValidatorInterface $validator
    ) {
        $this->encoder = $encoder;
        $this->decoder = $decoder;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     */
    public function encode(
        array $data,
        ?array $namespace = null,
        ?DOMElement $parent = null,
        ?Xml $doc = null
    ): Xml {
        return $this->encoder->encode(
            $data,
            $namespace,
            $parent,
            $doc
        );
    }

    /**
     * {@inheritdoc}
     */
    public function decode(
        Xml|DOMElement $doc,
        ?array &$data = null,
        bool $twinsAsArray = false
    ): array {
        return $this->decoder->decode(
            $doc,
            $data,
            $twinsAsArray
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateSchema(
        Xml $xml,
        ?string $schemaPath = null,
        array $translations = []
    ): void {
        $this->validator->validateSchema(
            $xml,
            $schemaPath,
            $translations
        );
    }
}
