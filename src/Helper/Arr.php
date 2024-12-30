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

namespace Derafu\Lib\Core\Helper;

use Illuminate\Support\Arr as IlluminateArr;

/**
 * Clase para trabajar con arreglos.
 */
class Arr extends IlluminateArr
{
    /**
     * Une dos arreglos recursivamente manteniendo solo una clave.
     *
     * @param array $array1 El primer arreglo.
     * @param array $array2 El segundo arreglo.
     * @return array El arreglo resultante de la unión.
     */
    public static function mergeRecursiveDistinct(
        array $array1,
        array $array2
    ): array {
        $merged = $array1;
        foreach ($array2 as $key => &$value) {
            if (
                is_array($value)
                && isset($merged[$key])
                && is_array($merged[$key])
            ) {
                $merged[$key] = self::mergeRecursiveDistinct(
                    $merged[$key],
                    $value
                );
            } else {
                $merged[$key] = $value;
            }
        }
        return $merged;
    }
}