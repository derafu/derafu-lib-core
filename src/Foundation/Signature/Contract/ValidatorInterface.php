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

namespace Derafu\Lib\Core\Foundation\Signature\Contract;

use Derafu\Lib\Core\Foundation\Xml\Entity\Xml;

/**
 * Interfaz para la clase que maneja la validación de firmas electrónicas.
 */
interface ValidatorInterface
{
    /**
     * Verifica la firma digital de datos.
     *
     * @param string $data Datos que se desean verificar.
     * @param string $signature Firma digital de los datos en base64.
     * @param string $publicKey Clave pública de la firma de los datos.
     * @param string|int $signatureAlgorithm Algoritmo que se usó para firmar
     * (por defecto SHA1).
     * @return bool `true` si la firma es válida, `false` si es inválida.
     * @throws SignatureException Si hubo un error al hacer la verificación.
     */
    public function validate(
        string $data,
        string $signature,
        string $publicKey,
        string|int $signatureAlgorithm = OPENSSL_ALGO_SHA1
    ): bool;

    /**
     * Verifica la validez de la firma de un XML utilizando RSA y SHA1.
     *
     * @param Xml|string $xml String XML que se desea validar.
     * @return void
     * @throws SignatureException Si hubo un error al hacer la verificación.
     */
    public function validateXml(Xml|string $xml): void;
}
