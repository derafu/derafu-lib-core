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

namespace Derafu\Lib\Core\Foundation\Log\Storage;

use Derafu\Lib\Core\Foundation\Log\Contract\StorageInterface;
use Derafu\Lib\Core\Foundation\Log\Entity\Log;

/**
 * Almacenamiento en memoria para los registros de la bitácora.
 *
 * Los mensajes estarán disponibles solo durante la ejecución del script PHP.
 * Una vez termina, los mensajes se pierden. Es importante recuperarlos antes
 * de que termine la ejecución del script PHP si se desea hacer algo con ellos.
 */
class InMemoryStorage implements StorageInterface
{
    /**
     * Almacenamiento en memoria para los registros de la bitácora.
     *
     * @var array
     */
    private array $buffer;

    /**
     * {@inheritdoc}
     */
    public function write(Log $log): void
    {
        $this->buffer[] = $log;
    }

    /**
     * {@inheritdoc}
     */
    public function all(): array
    {
        return $this->buffer;
    }

    /**
     * {@inheritdoc}
     */
    public function clear(): void
    {
        $this->buffer = [];
    }
}
