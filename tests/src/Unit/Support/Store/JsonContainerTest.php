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

namespace Derafu\Lib\Tests\Unit\Support\Store;

use Derafu\Lib\Core\Helper\Selector;
use Derafu\Lib\Core\Support\Store\JsonContainer;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(JsonContainer::class)]
#[CoversClass(Selector::class)]
class JsonContainerTest extends TestCase
{
    public static function provideTestCases(): array
    {
        $tests = require self::getFixturesPath(
            'support/storage/json_container.php'
        );

        $testCases = [];

        foreach ($tests as $name => $test) {
            $testCases[$name] = [
                $test['data'],
                $test['schema'],
                $test['expected'],
            ];
        }

        return $testCases;
    }

    public function testJsonContainerSchemaGetterAndSetter(): void
    {
        $schema = [
            'required' => ['foo'],
            'properties' => [
                'foo' => [
                    'type' => 'string',
                ],
            ],
        ];

        $expected = [
            '$schema' => 'https://json-schema.org/draft/2020-12/schema',
            'type' => 'object',
            'required' => ['foo'],
            'properties' => [
                'foo' => [
                    'type' => 'string',
                ],
            ],
        ];

        $container = new JsonContainer();
        $container->setSchema($schema);
        $this->assertSame($expected, $container->getSchema());
    }

    #[DataProvider('provideTestCases')]
    public function testJsonContainerCase(
        array $data,
        array $schema,
        array|string $expected
    ): void {
        if (is_string($expected)) {
            $this->expectException($expected);
        }

        $container = new JsonContainer($data, $schema);

        foreach ($expected as $selector => $expectedValue) {
            $this->assertSame($expectedValue, $container->get($selector));
        }
    }
}
