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

namespace Derafu\Lib\Core\Foundation\Log\Abstract;

use Derafu\Lib\Core\Foundation\Log\Contract\LogServiceInterface;
use Derafu\Lib\Core\Foundation\Log\Entity\Caller;
use Derafu\Lib\Core\Foundation\Log\Entity\Level;
use Monolog\Logger;

/**
 * Clase base de LoggerServiceInterface con la implementación de los métodos que
 * dependen de otros que si se han de implementar y que son requeridos por la
 * interfaz.
 *
 * No es obligatorio usar esta clase abstracta pero ahorra código al ya tener
 * incluídos muchos métodos de la interfaz que dependen de otros de la misma
 * interfaz.
 */
abstract class AbstractLogService implements LogServiceInterface
{
    /**
     * Instancia del logger de Monolog.
     *
     * @var Logger
     */
    protected Logger $logger;

    /**
     * Define si se guarda o no quién llamó al log.
     *
     * @var bool
     */
    private bool $saveCaller = true;

    /**
     * Activa o desactiva el guardar quién llamó al log para los mensajes que se
     * escribirán en la bitácora.
     *
     * @param bool $saveCaller Define si se debe o no guardar el caller.
     * @return void
     */
    public function saveCaller(bool $saveCaller = true): void
    {
        $this->saveCaller = $saveCaller;
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
    public function flushLogs(
        int|string|null $level = null,
        bool $newFirst = true
    ): array {
        $logs = $this->getLogs($level, $newFirst);
        $this->clearLogs($level);

        return $logs;
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
     * {@inheritdoc}
     */
    public function getDebugLogs(): array
    {
        return $this->getLogs(Level::DEBUG);
    }

    /**
     * {@inheritdoc}
     */
    public function getInfoLogs(): array
    {
        return $this->getLogs(Level::INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function getNoticeLogs(): array
    {
        return $this->getLogs(Level::NOTICE);
    }

    /**
     * {@inheritdoc}
     */
    public function getWarningLogs(): array
    {
        return $this->getLogs(Level::WARNING);
    }

    /**
     * {@inheritdoc}
     */
    public function getErrorLogs(): array
    {
        return $this->getLogs(Level::ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function getCriticalLogs(): array
    {
        return $this->getLogs(Level::CRITICAL);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlertLogs(): array
    {
        return $this->getLogs(Level::ALERT);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmergencyLogs(): array
    {
        return $this->getLogs(Level::EMERGENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function clearDebugLogs(): void
    {
        $this->clearLogs(Level::DEBUG);
    }

    /**
     * {@inheritdoc}
     */
    public function clearInfoLogs(): void
    {
        $this->clearLogs(Level::INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function clearNoticeLogs(): void
    {
        $this->clearLogs(Level::NOTICE);
    }

    /**
     * {@inheritdoc}
     */
    public function clearWarningLogs(): void
    {
        $this->clearLogs(Level::WARNING);
    }

    /**
     * {@inheritdoc}
     */
    public function clearErrorLogs(): void
    {
        $this->clearLogs(Level::ERROR);
    }

    /**
     * {@inheritdoc}
     */
    public function clearCriticalLogs(): void
    {
        $this->clearLogs(Level::CRITICAL);
    }

    /**
     * {@inheritdoc}
     */
    public function clearAlertLogs(): void
    {
        $this->clearLogs(Level::ALERT);
    }

    /**
     * {@inheritdoc}
     */
    public function clearEmergencyLogs(): void
    {
        $this->clearLogs(Level::EMERGENCY);
    }

    /**
     * {@inheritdoc}
     */
    public function flushDebugLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::DEBUG, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushInfoLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::INFO, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushNoticeLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::NOTICE, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushWarningLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::WARNING, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushErrorLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::ERROR, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushCriticalLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::CRITICAL, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushAlertLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::ALERT, $newFirst);
    }

    /**
     * {@inheritdoc}
     */
    public function flushEmergencyLogs(bool $newFirst = true): array
    {
        return $this->flushLogs(Level::EMERGENCY, $newFirst);
    }

    /**
     * Normaliza el contexto del registro de la bitácora.
     *
     * @param array $context
     * @return array
     */
    private function normalizeContext(array $context): array
    {
        // Se agrega el caller si se ha solicitado que se guarde.
        if ($this->saveCaller) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $context['__caller'] = new Caller(
                file: $trace[1]['file'] ?? null,
                line: $trace[1]['line'] ?? null,
                function: $trace[2]['function'] ?? null,
                class: $trace[2]['class'] ?? null,
                type: $trace[2]['type'] ?? null
            );
        }

        // Entregar el contexto normalizado.
        return $context;
    }
}
