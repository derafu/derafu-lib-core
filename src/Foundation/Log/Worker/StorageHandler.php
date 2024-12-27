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

namespace Derafu\Lib\Core\Foundation\Log\Worker;

use Derafu\Lib\Core\Foundation\Log\Contract\StorageInterface;
use Derafu\Lib\Core\Foundation\Log\Entity\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\LogRecord as MonologLogRecord;

/**
 * Clase para manejar los mensajes de la bitácora.
 */
class StorageHandler extends AbstractProcessingHandler
{
    /**
     * Instancia del almacenamiento de los logs.
     *
     * @var StorageInterface
     */
    private StorageInterface $storage;

    /**
     * Constructor de la clase.
     */
    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Agrega un registro de la bitácora al almacenamiento.
     *
     * @param MonologLogRecord $logRecord
     */
    public function write(MonologLogRecord $logRecord): void
    {
        if (!($logRecord instanceof Log)) {
            $processor = new Processor();
            $logRecord = $processor($logRecord);
        }

        $this->storage->write($logRecord);
    }
}
