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

namespace Derafu\Lib\Core\Package\Prime\Component\Log\Worker;

use Derafu\Lib\Core\Foundation\Abstract\AbstractWorker;
use Derafu\Lib\Core\Package\Prime\Component\Log\Contract\LoggerWorkerInterface;
use Derafu\Lib\Core\Package\Prime\Component\Log\Entity\Caller;
use Derafu\Lib\Core\Package\Prime\Component\Log\Entity\Level;
use Derafu\Lib\Core\Support\Store\Contract\JournalInterface;
use Derafu\Lib\Core\Support\Store\Journal;
use Monolog\Formatter\FormatterInterface;
use Monolog\Logger;

/**
 * Clase de LoggerServiceInterface con la implementación del sistema de log.
 */
class LoggerWorker extends AbstractWorker implements LoggerWorkerInterface
{
    /**
     * Instancia del logger de Monolog.
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Instancia del almacenamiento de los logs.
     *
     * @var JournalInterface
     */
    private JournalInterface $journal;

    /**
     * Constructor del worker.
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
        $this->logger = new Logger($channel);

        // Crear el procesador de los registros de la bitácora.
        $processor = new Processor();
        $this->logger->pushProcessor($processor);

        // Crear el almacenamiento de los registros de la bitácora.
        $this->journal = new Journal();

        // Crear el handler de almacenamiento en memoria de los logs.
        $journalHandler = new JournalHandler($this->journal);
        if ($formatter !== null) {
            $journalHandler->setFormatter($formatter);
        }
        $handlers[] = $journalHandler;

        // Agregar los handlers.
        foreach ($handlers as $handler) {
            $this->logger->pushHandler($handler);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJournal(): JournalInterface
    {
        return $this->journal;
    }

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        $level = (new Level($level))->getMonologLevel();
        $context = $this->normalizeContext($context);
        $this->logger->log($level, $message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->debug($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function info($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->info($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function notice($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->notice($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function warning($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->warning($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function error($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->error($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function critical($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->critical($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function alert($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->alert($message, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function emergency($message, array $context = []): void
    {
        $context = $this->normalizeContext($context);
        $this->logger->emergency($message, $context);
    }

    /**
     * Normaliza el contexto del registro de la bitácora.
     *
     * @param array $context
     * @return array
     */
    private function normalizeContext(array $context): array
    {
        // Se agrega el caller.
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $context['__caller'] = new Caller(
            file: $trace[1]['file'] ?? null,
            line: $trace[1]['line'] ?? null,
            function: $trace[2]['function'] ?? null,
            class: $trace[2]['class'] ?? null,
            type: $trace[2]['type'] ?? null
        );

        // Entregar el contexto normalizado.
        return $context;
    }
}
