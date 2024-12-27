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

namespace Derafu\Lib\Core\Foundation\Log;

use Derafu\Lib\Core\Foundation\Log\Abstract\AbstractLogService;
use Derafu\Lib\Core\Foundation\Log\Contract\StorageInterface;
use Derafu\Lib\Core\Foundation\Log\Storage\InMemoryStorage;
use Derafu\Lib\Core\Foundation\Log\Worker\Processor;
use Derafu\Lib\Core\Foundation\Log\Worker\StorageHandler;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;

/**
 * Servicio de registro de bitácora (logging).
 */
class LogService extends AbstractLogService
{
    /**
     * Nombre del canal que se usará en Monolog para asociar los logs.
     *
     * @var string
     */
    private string $channel;

    /**
     * Instancia del almacenamiento de los logs.
     *
     * @var StorageInterface
     */
    private StorageInterface $storage;

    /**
     * Constructor del servicio.
     *
     * @param string $channel Nombre del canal para el logger.
     * @param FormatterInterface|null $formatter Formatter para los logs.
     * @param array $handlers Lista de handlers adicionales.
     */
    public function __construct(
        string $channel = 'derafu_lib',
        ?FormatterInterface $formatter = null,
        array $handlers = []
    ) {
        // Crear el logger.
        $this->channel = $channel;
        $this->logger = new Logger($this->channel);

        // Crear el procesador de los registros de la bitácora.
        $processor = new Processor();
        $this->logger->pushProcessor($processor);

        // Crear el almacenamiento de los registros de la bitácora.
        $this->storage = new InMemoryStorage();

        // Crear el handler de almacenamiento en memoria de los logs.
        $memoryHandler = new StorageHandler($this->storage);
        if ($formatter !== null) {
            $memoryHandler->setFormatter($formatter);
        }
        $handlers[] = $memoryHandler;

        // Agregar los handlers.
        foreach ($handlers as $handler) {
            $this->logger->pushHandler($handler);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLogs(int $level = null, bool $newFirst = true): array
    {
        $records = $this->storage->all();

        if ($level === null) {
            return $newFirst ? array_reverse($records) : $records;
        }

        $filtered = [];
        foreach ($records as $record) {
            if ($record->level->value === $level) {
                $filtered[] = $record;
            }
        }

        return $newFirst ? array_reverse($filtered) : $filtered;
    }

    /**
     * {@inheritdoc}
     */
    public function clearLogs(?int $level = null): void
    {
        // Borrar todos los logs.
        if ($level === null) {
            $this->storage->clear();
            return;
        }

        // Dejar todos los logs excepto los de cierto nivel (esos se borran).
        $logs = array_filter(
            $this->storage->all(),
            fn ($log) => $log->level->value !== $level
        );
        $this->storage->clear();
        foreach ($logs as $logRecord) {
            $this->storage->write($logRecord);
        }
    }
}
