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

namespace Derafu\Lib\Core\Support\Util;

use Illuminate\Support\Str as IlluminateStr;

/**
 * Clase para trabajar con strings.
 */
class Str extends IlluminateStr
{
    /**
     * Ancho por defecto al aplicar la función wordWrap().
     */
    public const WORDWRAP = 64;

    /**
     * Corta el string a un largo fijo por línea.
     *
     * @param string $string String a recortar.
     * @param integer $characters Ancho, o largo, máximo de cada línea.
     * @param string $break Caracter para el "corte" o salto de línea.
     * @param boolean $cutLongWords Si se deben cortar igual palabras largas.
     * @return string String en varias líneas ajustado al largo solicitado.
     */
    public static function wordWrap(
        $string,
        $characters = self::WORDWRAP,
        $break = "\n",
        $cutLongWords = true
    ) {
        return parent::wordWrap($string, $characters, $break, $cutLongWords);
    }

    /**
     * Convierte un string desde UTF-8 a ISO-8859-1.
     *
     * Si el string pasado no está codificado en UTF-8 se retornará el
     * string origial.
     *
     * @param string $string String a convertir en UTF-8.
     * @return string String en ISO-8859-1 si se logró convertir.
     */
    public static function utf8decode(string $string): string
    {
        if (!mb_detect_encoding($string, 'UTF-8', true)) {
            return $string;
        }

        $result = mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
        if ($result === false) {
            return $string;
        }

        return $result;
    }

    /**
     * Convierte un string desde ISO-8859-1 a UTF-8.
     *
     * Si el string pasado no está codificado en ISO-8859-1 se retornará el
     * string origial.
     *
     * @param string $string String a convertir en ISO-8859-1.
     * @return string String en UTF-8 si se logró convertir.
     */
    public static function utf8encode(string $string): string
    {
        if (!mb_detect_encoding($string, 'ISO-8859-1', true)) {
            return $string;
        }

        $result = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
        if ($result === false) {
            return $string;
        }

        return $result;
    }
}
