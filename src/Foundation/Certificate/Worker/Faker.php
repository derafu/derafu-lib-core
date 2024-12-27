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

namespace Derafu\Lib\Core\Foundation\Certificate\Worker;

use Derafu\Lib\Core\Foundation\Certificate\Contract\FakerInterface;
use Derafu\Lib\Core\Foundation\Certificate\Contract\LoaderInterface;
use Derafu\Lib\Core\Foundation\Certificate\Entity\Certificate;
use Derafu\Lib\Core\Foundation\Certificate\Exception\CertificateException;
use OpenSSLAsymmetricKey;

/**
 * Clase que se encarga de generar certificados autofirmados y retornarlos como
 * un string de datos, un arreglo o una instancia de Certificate.
 */
class Faker implements FakerInterface
{
    /**
     * Datos del sujeto del certificado.
     *
     * @var array
     */
    private array $subject;

    /**
     * Datos del emisor del certificado.
     *
     * @var array
     */
    private array $issuer;

    /**
     * Validez del certificado en formato UNIX timestamp.
     *
     * @var array
     */
    private array $validity;

    /**
     * Contraseña para proteger la clave privada en el certificado.
     *
     * @var string
     */
    private string $password;

    /**
     * Instancia que permite cargar (crear) certificados a partir de sus datos.
     *
     * @var LoaderInterface
     */
    private LoaderInterface $loader;

    /**
     * Constructor de la clase.
     *
     * Establece valores por defecto para el sujeto, emisor, validez y
     * contraseña.
     *
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;

        $this->setSubject();
        $this->setIssuer();
        $this->setValidity();
        $this->setPassword();
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(
        string $C = 'CL',
        string $ST = 'Colchagua',
        string $L = 'Santa Cruz',
        string $O = 'Organización Intergaláctica de Robots',
        string $OU = 'Tecnología',
        string $CN = 'Daniel',
        string $emailAddress = 'daniel.bot@example.com',
        string $serialNumber = '11222333-9',
        string $title = 'Bot',
    ): self {
        if (empty($CN) || empty($emailAddress) || empty($serialNumber)) {
            throw new CertificateException(
                'El CN, emailAddress y serialNumber son obligatorios.'
            );
        }

        $this->subject = [
            'C' => $C,
            'ST' => $ST,
            'L' => $L,
            'O' => $O,
            'OU' => $OU,
            'CN' => $CN,
            'emailAddress' => $emailAddress,
            'serialNumber' => strtoupper($serialNumber),
            'title' => $title,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setIssuer(
        string $C = 'CL',
        string $ST = 'Colchagua',
        string $L = 'Santa Cruz',
        string $O = 'LibreDTE',
        string $OU = 'Facturación Electrónica',
        string $CN = 'LibreDTE Autoridad Certificadora de Pruebas',
        string $emailAddress = 'fakes-certificates@libredte.cl',
        string $serialNumber = '76192083-9',
    ): self {
        $this->issuer = [
            'C' => $C,
            'ST' => $ST,
            'L' => $L,
            'O' => $O,
            'OU' => $OU,
            'CN' => $CN,
            'emailAddress' => $emailAddress,
            'serialNumber' => strtoupper($serialNumber),
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setValidity(int $days = 365): self
    {
        $this->validity = [
            'days' => $days,
        ];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setPassword(string $password = 'i_love_libredte')
    {
        $this->password = $password;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function createAsString(): string
    {
        // Días de validez del certificado (emisor y sujeto).
        $days = $this->validity['days'];

        // Crear clave privada y CSR para el emisor.
        $issuerPrivateKey = openssl_pkey_new();
        if (!$issuerPrivateKey instanceof OpenSSLAsymmetricKey) {
            throw new CertificateException(
                'No fue posible generar la llave privada del emisor del certificado.'
            );
        }
        $issuerCsr = openssl_csr_new($this->issuer, $issuerPrivateKey);

        // Crear certificado autofirmado para el emisor (CA).
        $issuerCert = openssl_csr_sign(
            $issuerCsr,         // CSR del emisor.
            null,               // Certificado emisor (null indica que es autofirmado).
            $issuerPrivateKey,  // Clave privada del emisor.
            $days,              // Número de días de validez (misma sujeto).
            [],                 // Opciones adicionales.
            666                 // Número de serie del certificado.
        );

        // Validar que se haya podido crear el certificado del emisor (CA).
        if ($issuerCert === false) {
            throw new CertificateException(
                'No fue posible generar el certificado del emisor (CA).'
            );
        }

        // Crear clave privada y CSR para el sujeto.
        $subjectPrivateKey = openssl_pkey_new();
        if (!$subjectPrivateKey instanceof OpenSSLAsymmetricKey) {
            throw new CertificateException(
                'No fue posible generar la llave privada del certificado.'
            );
        }
        $subjectCsr = openssl_csr_new($this->subject, $subjectPrivateKey);

        // Usar el certificado del emisor para firmar el CSR del sujeto.
        $subjectCert = openssl_csr_sign(
            $subjectCsr,        // La solicitud de firma del certificado (CSR).
            $issuerCert,        // Certificado emisor.
            $issuerPrivateKey,  // Clave privada del emisor.
            $days,              // Número de días de validez.
            [],                 // Opciones adicionales.
            69                  // Número de serie del certificado.
        );

        // Validar que se haya podido crear el certificado del usuario.
        if ($subjectCert === false) {
            throw new CertificateException(
                'No fue posible generar el certificado del usuario.'
            );
        }

        // Exportar el certificado final en formato PKCS#12.
        openssl_pkcs12_export(
            $subjectCert,
            $data,
            $subjectPrivateKey,
            $this->password
        );

        // Entregar los datos del certificado digital.
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function createAsArray(): array
    {
        $data = $this->createAsString();
        $array = [];
        openssl_pkcs12_read($data, $array, $this->password);

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function create(): Certificate
    {
        $array = $this->createAsArray();

        return $this->loader->createFromArray($array);
    }
}
