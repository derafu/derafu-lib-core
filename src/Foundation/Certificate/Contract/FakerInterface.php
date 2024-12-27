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

namespace Derafu\Lib\Core\Foundation\Certificate\Contract;

use Derafu\Lib\Core\Foundation\Certificate\Entity\Certificate;

/**
 * Interfaz para la clase que permite crear un certificado digital autofirmado.
 */
interface FakerInterface
{
    /**
     * Configura los datos del sujeto del certificado.
     *
     * @param string $C País del sujeto.
     * @param string $ST Estado o provincia del sujeto.
     * @param string $L Localidad del sujeto.
     * @param string $O Organización del sujeto.
     * @param string $OU Unidad organizativa del sujeto.
     * @param string $CN Nombre común del sujeto.
     * @param string $emailAddress Correo electrónico del sujeto.
     * @param string $serialNumber Número de serie del sujeto.
     * @param string $title Título del sujeto.
     * @return self
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
    ): self;

    /**
     * Configura los datos del emisor del certificado.
     *
     * @param string $C País del emisor.
     * @param string $ST Estado o provincia del emisor.
     * @param string $L Localidad del emisor.
     * @param string $O Organización del emisor.
     * @param string $OU Unidad organizativa del emisor.
     * @param string $CN Nombre común del emisor.
     * @param string $emailAddress Correo electrónico del emisor.
     * @param string $serialNumber Número de serie del emisor.
     * @return self
     */
    public function setIssuer(
        string $C = 'CL',
        string $ST = 'Colchagua',
        string $L = 'Santa Cruz',
        string $O = 'Derafu',
        string $OU = 'Tecnología',
        string $CN = 'Derafu Autoridad Certificadora de Pruebas',
        string $emailAddress = 'fakes-certificates@derafu.org',
        string $serialNumber = '76192083-9',
    ): self;

    /**
     * Configura la validez del certificado.
     *
     * @param int $days Días que el certificado será válido desde la
     * fecha actual. Si no se proporciona, tendrá validez de 365 días.
     * @return self
     */
    public function setValidity(int $days = 365): self;

    /**
     * Configura la contraseña para proteger la clave privada.
     *
     * @param string $password Contraseña para proteger la clave privada.
     * @return void
     */
    public function setPassword(string $password = 'i_love_derafu');

    /**
     * Obtiene la contraseña configurada.
     *
     * @return string Contraseña configurada.
     */
    public function getPassword(): string;

    /**
     * Genera un certificado digital en formato PKCS#12 y lo devuelve como un
     * string.
     *
     * @return string Certificado digital en formato PKCS#12.
     */
    public function createAsString(): string;

    /**
     * Genera un certificado digital en formato PKCS#12 y lo devuelve como un
     * arreglo.
     *
     * @return array Certificado digital en formato PKCS#12.
     */
    public function createAsArray(): array;

    /**
     * Genera un certificado digital y lo devuelve como una instancia de
     * Certificate.
     *
     * @return Certificate Instancia de Certificate.
     */
    public function create(): Certificate;
}
