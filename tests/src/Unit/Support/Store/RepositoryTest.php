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

use Derafu\Lib\Core\Support\Store\Repository;
use Derafu\Lib\Tests\TestCase;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Repository::class)]
class RepositoryTest extends TestCase
{
    public static function provideTestCases(): array
    {
        $tests = require self::getFixturesPath(
            'support/store/repository.php'
        );

        $testCases = [];

        foreach ($tests as $name => $test) {
            foreach ($test['cases'] as $caseName => $case) {
                $testCases[$name . ':' . $caseName] = [
                    $test['data'],
                    $case,
                ];
            }
        }

        return $testCases;
    }

    #[DataProvider('provideTestCases')]
    public function testRepositoryCase(array $data, array $case): void
    {
        $repository = new Repository($data);

        $method = $case['method'];
        $args = $case['args'];
        $expected = $case['expected'];

        $result = match($method) {
            'find' => $repository->find(...$args),
            'findAll' => $repository->findAll(),
            'findBy' => $repository->findBy(...$args),
            'findOneBy' => $repository->findOneBy(...$args),
            'count' => $repository->count(...$args),
            default => throw new InvalidArgumentException(sprintf(
                'Método %s no soportado.',
                $method
            ))
        };

        if (is_object($expected)) {
            $expected = (array) $expected;
        }

        if (is_object($result)) {
            $result = (array) $result;
        }

        if (is_array($result)) {
            $result = array_map(fn ($obj) => (array) $obj, $result);
        }

        if (is_array($expected)) {
            $expected = array_map(fn ($obj) => (array) $obj, $expected);
        }

        $this->assertSame($expected, $result);
    }
}
