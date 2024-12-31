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

namespace Derafu\Lib\Core\Package\Prime;

use Derafu\Lib\Core\Foundation\Abstract\AbstractPackage;
use Derafu\Lib\Core\Package\Prime\Component\Certificate\Contract\CertificateComponentInterface;
use Derafu\Lib\Core\Package\Prime\Component\Entity\Contract\EntityComponentInterface;
use Derafu\Lib\Core\Package\Prime\Component\Log\Contract\LogComponentInterface;
use Derafu\Lib\Core\Package\Prime\Component\Signature\Contract\SignatureComponentInterface;
use Derafu\Lib\Core\Package\Prime\Component\Xml\Contract\XmlComponentInterface;
use Derafu\Lib\Core\Package\Prime\Contract\PrimePackageInterface;

/**
 * Clase del paquete Prime.
 */
class PrimePackage extends AbstractPackage implements PrimePackageInterface
{
    /**
     * Constructor del paquete.
     *
     * @param CertificateComponentInterface $certificate
     * @param LogComponentInterface $log
     * @param SignatureComponentInterface $signature
     * @param XmlComponentInterface $xml
     */
    public function __construct(
        private CertificateComponentInterface $certificate,
        private EntityComponentInterface $entity,
        private LogComponentInterface $log,
        private SignatureComponentInterface $signature,
        private XmlComponentInterface $xml,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getComponents(): array
    {
        return [
            'certificate' => $this->certificate,
            'entity' => $this->entity,
            'log' => $this->log,
            'signature' => $this->signature,
            'xml' => $this->xml,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateComponent(): CertificateComponentInterface
    {
        return $this->certificate;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityComponent(): EntityComponentInterface
    {
        return $this->entity;
    }

    /**
     * {@inheritdoc}
     */
    public function getLogComponent(): LogComponentInterface
    {
        $config = $this->getComponentConfiguration('log');

        return $this->log->setConfiguration($config);
    }

    /**
     * {@inheritdoc}
     */
    public function getSignatureComponent(): SignatureComponentInterface
    {
        return $this->signature;
    }

    /**
     * {@inheritdoc}
     */
    public function getXmlComponent(): XmlComponentInterface
    {
        return $this->xml;
    }
}
