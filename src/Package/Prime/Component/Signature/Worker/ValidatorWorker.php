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

namespace Derafu\Lib\Core\Package\Prime\Component\Signature\Worker;

use Derafu\Lib\Core\Foundation\Abstract\AbstractWorker;
use Derafu\Lib\Core\Helper\AsymmetricKey;
use Derafu\Lib\Core\Package\Prime\Component\Signature\Contract\GeneratorWorkerInterface;
use Derafu\Lib\Core\Package\Prime\Component\Signature\Contract\ValidatorWorkerInterface;
use Derafu\Lib\Core\Package\Prime\Component\Signature\Entity\Signature;
use Derafu\Lib\Core\Package\Prime\Component\Signature\Exception\SignatureException;
use Derafu\Lib\Core\Package\Prime\Component\Xml\Contract\XmlComponentInterface;
use Derafu\Lib\Core\Package\Prime\Component\Xml\Entity\Xml;

/**
 * Clase que maneja la validación de firmas electrónicas.
 */
class ValidatorWorker extends AbstractWorker implements ValidatorWorkerInterface
{
    /**
     * Generador de firmas electrónicas.
     *
     * @var GeneratorWorkerInterface
     */
    private GeneratorWorkerInterface $generator;

    /**
     * Servicio de XMLs.
     *
     * @var XmlComponentInterface
     */
    private XmlComponentInterface $xmlComponent;

    /**
     * Constructor del validador de firmas electrónicas.
     *
     * @param GeneratorWorkerInterface $generator
     * @param XmlComponentInterface $xmlComponent
     */
    public function __construct(
        GeneratorWorkerInterface $generator,
        XmlComponentInterface $xmlComponent
    ) {
        $this->generator = $generator;
        $this->xmlComponent = $xmlComponent;
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
        $publicKey = AsymmetricKey::normalizePublicKey($publicKey);

        $result = openssl_verify(
            $data,
            base64_decode($signature),
            $publicKey,
            $signatureAlgorithm
        );

        if ($result === -1) {
            throw new SignatureException(
                'Ocurrió un error al verificar la firma electrónica de los datos.'
            );
        }

        return $result === 1;
    }

    /**
     * {@inheritdoc}
     */
    public function validateXml(Xml|string $xml): void
    {
        // Si se pasó un objeto Xml se convierte a string.
        if (!is_string($xml)) {
            $xml = $xml->saveXml();
        }

        // Cargar el string XML en un documento XML.
        $doc = new Xml();
        $doc->loadXml($xml);
        if (!$doc->documentElement) {
            throw new SignatureException(
                'No se pudo obtener el documentElement desde el XML para validar su firma (posible XML mal formado).'
            );
        }

        // Buscar todos los elementos que sean tag Signature.
        // Un documento XML puede tener más de una firma.
        $signaturesElements = $doc->documentElement->getElementsByTagName(
            'Signature'
        );

        // Si no se encontraron firmas en el XML error.
        if (!$signaturesElements->length) {
            throw new SignatureException(
                'No se encontraron firmas que validar en el XML.'
            );
        }

        // Iterar cada firma encontrada.
        foreach ($signaturesElements as $signatureElement) {
            // Armar instancia del nodo de la firma.
            $signature = new Signature();
            $this->loadXmlOnSignatureNode(
                $signature,
                $signatureElement->C14N()
            );

            // Validar el nodo de la firma electrónica.
            $this->validateSignature($doc, $signature);
        }
    }

    /**
     * Crea la instancia `Xml` de `Signature` a partir de un
     * string XML con el nodo de la firma.
     *
     * @param Signature $signatureNode
     * @param string $xml String con el XML del nodo `Signature'.
     */
    private function loadXmlOnSignatureNode(
        Signature $signatureNode,
        string $xml
    ): void {
        $signature = new Xml();
        $signature->formatOutput = false;
        $signature->loadXml($xml);

        $data = $this->xmlComponent->getDecoderWorker()->decode(
            $signature
        );

        // El orden es importante, pues setData() invalida el Xml si
        // estaba previamente asignado.
        $signatureNode->setData($data);
        $signatureNode->setXml($signature);
    }

    /**
     * Valida el nodo de la firma electrónica del XML.
     *
     * Valida el DigestValue y la firma de dicho DigestValue.
     *
     * @param Xml $xml Documento XML que se desea validar.
     * @param Signature $signatureNode Nodo de firma que se validará.
     * @return void
     * @throws SignatureException En caso de error de DigestValue o firma.
     */
    private function validateSignature(
        Xml $xml,
        Signature $signatureNode
    ): void {
        $this->validateSignatureDigestValue($xml, $signatureNode);
        $this->validateSignatureSignatureValue($signatureNode);
    }

    /**
     * Validar DigestValue de los datos firmados.
     *
     * @param Xml|string $xml Documento XML que se desea validar.
     * @param Signature $signatureNode Nodo de firma que se validará.
     * @return void
     * @throws SignatureException Si el DigestValue no es válido.
     */
    private function validateSignatureDigestValue(
        Xml|string $xml,
        Signature $signatureNode
    ): void {
        // Si se pasó un objeto Xml se convierte a string.
        if (!is_string($xml)) {
            $xml = $xml->saveXml();
        }

        // Cargar el string XML en un documento XML.
        $doc = new Xml();
        $doc->loadXml($xml);
        if (!$doc->documentElement) {
            throw new SignatureException(
                'No se pudo obtener el documentElement desde el XML para validar su firma (posible XML mal formado).'
            );
        }

        // Obtener digest que viene en en el XML (en el nodo de la firma).
        $digestValueXml = $signatureNode->getDigestValue();

        // Calcular el digest a partir del documento XML.
        $digestValueCalculated = $this->generator->digestXmlReference(
            $doc,
            $signatureNode->getReference()
        );

        // Si los digest no coinciden no es válido.
        if ($digestValueXml !== $digestValueCalculated) {
            throw new SignatureException(sprintf(
                'El DigestValue que viene en el XML "%s" para la referencia "%s" no coincide con el valor calculado al validar "%s". Los datos de la referencia podrían haber sido manipulados después de haber sido firmados.',
                $digestValueXml,
                $signatureNode->getReference(),
                $digestValueCalculated
            ));
        }
    }

    /**
     * Valida la firma del nodo `SignedInfo` del XML utilizando el certificado
     * X509.
     *
     * @param Signature $signatureNode Nodo de firma que se validará.
     * @throws SignatureException Si la firma electrónica del XML no es válida.
     */
    private function validateSignatureSignatureValue(
        Signature $signatureNode
    ): void {
        // Generar el string XML de los datos que se validará su firma.
        $xpath = "//*[local-name()='Signature']/*[local-name()='SignedInfo']";
        $signedInfoC14N = $signatureNode
            ->getXml()
            ->C14NWithIsoEncoding($xpath)
        ;

        // Validar firma electrónica.
        $isValid = $this->validate(
            $signedInfoC14N,
            $signatureNode->getSignatureValue(),
            $signatureNode->getX509Certificate()
        );

        // Si la firma electrónica no es válida se lanza una excepción.
        if (!$isValid) {
            throw new SignatureException(sprintf(
                'La firma electrónica del nodo `SignedInfo` del XML para la referencia "%s" no es válida.',
                $signatureNode->getReference()
            ));
        }
    }
}
