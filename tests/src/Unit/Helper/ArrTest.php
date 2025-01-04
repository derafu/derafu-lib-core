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

namespace Derafu\Lib\Tests\Unit\Helper;

use Derafu\Lib\Core\Helper\Arr;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Arr::class)]
class ArrTest extends TestCase
{
    public function testMergeRecursiveDistinctSimple(): void
    {
        $array1 = ['a' => 1, 'b' => 2];
        $array2 = ['b' => 3, 'c' => 4];

        $expected = ['a' => 1, 'b' => 3, 'c' => 4];
        $this->assertSame($expected, Arr::mergeRecursiveDistinct($array1, $array2));
    }

    public function testMergeRecursiveDistinctNested(): void
    {
        $array1 = [
            'a' => ['a1' => 1, 'a2' => 2],
            'b' => 2,
        ];
        $array2 = [
            'a' => ['a2' => 3, 'a3' => 4],
            'c' => 5,
        ];

        $expected = [
            'a' => ['a1' => 1, 'a2' => 3, 'a3' => 4],
            'b' => 2,
            'c' => 5,
        ];
        $this->assertSame($expected, Arr::mergeRecursiveDistinct($array1, $array2));
    }

    public function testMergeRecursiveDistinctOverwriting(): void
    {
        $array1 = ['a' => 1, 'b' => ['b1' => 2, 'b2' => 3]];
        $array2 = ['b' => ['b2' => 4, 'b3' => 5]];

        $expected = [
            'a' => 1,
            'b' => ['b1' => 2, 'b2' => 4, 'b3' => 5],
        ];
        $this->assertSame($expected, Arr::mergeRecursiveDistinct($array1, $array2));
    }

    public function testMergeRecursiveDistinctEmptyArray(): void
    {
        $array1 = ['a' => 1];
        $array2 = [];

        $expected = ['a' => 1];
        $this->assertSame($expected, Arr::mergeRecursiveDistinct($array1, $array2));
    }

    public function testMergeRecursiveDistinctBothEmpty(): void
    {
        $array1 = [];
        $array2 = [];

        $expected = [];
        $this->assertSame($expected, Arr::mergeRecursiveDistinct($array1, $array2));
    }

    public function testAutoCastRecursive()
    {
        $array = [
            'integerString' => '42',
            'floatString' => '42.42',
            'negativeIntegerString' => '-42',
            'negativeFloatString' => '-42.42',
            'emptyString' => '',
            'stringWithSpaces' => '   123   ',
            'nonNumericString' => 'hello',
            'arrayWithMixedValues' => [
                'nestedIntegerString' => '10',
                'nestedFloatString' => '10.10',
                'nestedEmptyString' => '',
                'nestedNonNumericString' => 'world',
            ],
            'nonStringValue' => true, // Should remain unchanged
        ];

        $expected = [
            'integerString' => 42,
            'floatString' => 42.42,
            'negativeIntegerString' => -42,
            'negativeFloatString' => -42.42,
            'emptyString' => null, // Default empty value for test
            'stringWithSpaces' => 123, // Trimmed and casted to int
            'nonNumericString' => 'hello', // Remains unchanged
            'arrayWithMixedValues' => [
                'nestedIntegerString' => 10,
                'nestedFloatString' => 10.10,
                'nestedEmptyString' => null, // Default empty value for test
                'nestedNonNumericString' => 'world', // Remains unchanged
            ],
            'nonStringValue' => true, // Remains unchanged
        ];

        $result = Arr::autoCastRecursive($array, null);

        $this->assertSame($expected, $result, 'The array was not transformed as expected.');
    }

    public function testAutoCastRecursiveWithCustomEmptyValue()
    {
        $array = [
            'emptyString' => '',
            'nestedArray' => [
                'nestedEmptyString' => '',
            ],
        ];

        $expected = [
            'emptyString' => 'customValue',
            'nestedArray' => [
                'nestedEmptyString' => 'customValue',
            ],
        ];

        $result = Arr::autoCastRecursive($array, 'customValue');

        $this->assertSame($expected, $result, 'The custom empty value was not applied correctly.');
    }

    public function testAutoCastRecursiveNoCastsUnnecessaryValues()
    {
        $array = [
            'booleanTrue' => true,
            'booleanFalse' => false,
            'nullValue' => null,
            'integer' => 123,
            'float' => 123.45,
        ];

        $expected = $array; // Should remain unchanged

        $result = Arr::autoCastRecursive($array);

        $this->assertSame($expected, $result, 'The non-string values were altered when they should not have been.');
    }
}
