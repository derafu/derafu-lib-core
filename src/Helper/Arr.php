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

    /**
     * Casta recursivamente y de manera automática los datos de un arreglo.
     *
     * Aplica las siguientes reglas a los valores que sean strings:
     *
     *   - Limpia los strings con trim().
     *   - Un string que representa un decimal es casteado como float.
     *   - Un string que representa un entero es casteado como int.
     *   - Un string vacío puede ser alterado con otro valor (por defecto se
     *     deja igual sin alterar).
     *
     * @param array $array Arreglo que se castearán sus strings según reglas.
     * @param mixed $emptyValue Valor que se asignará a strings vacíos.
     * @return array Arrego modificado.
     */
    public static function autoCastRecursive(array &$array, mixed $emptyValue = ''): array
    {
        array_walk_recursive($array, function (&$value, $key, $emptyValue) {
            if (is_string($value)) {
                $value = trim($value);
                if (is_numeric($value)) {
                    if ($value == (int) $value) {
                        $value = (int) $value;
                    } else {
                        $value = (float) $value;
                    }
                } elseif ($value === '') {
                    $value = $emptyValue;
                }
            }
        }, $emptyValue);

        return $array;
    }

    /**
     * Agrega el ID (índice del arreglo) a los datos del arreglo de dicho
     * índice.
     *
     * Esto hará que el ID esté tanto el índice como en los datos del índice.
     *
     * @param array<int|string,array> $data
     * @param string $idAttribute
     * @return array
     */
    public static function addIdAttribute(array $data, string $idAttribute): array
    {
        return array_combine(
            array_keys($data),
            array_map(
                fn ($id, $item) => array_merge(
                    [$idAttribute => $id],
                    $item
                ),
                array_keys($data),
                array_values($data)
            )
        );
    }
}
