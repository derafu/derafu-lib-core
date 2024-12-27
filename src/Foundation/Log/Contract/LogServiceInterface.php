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

namespace Derafu\Lib\Core\Foundation\Log\Contract;

use Psr\Log\LoggerInterface;

/**
 * Interfaz para la clase de registros en bitácora (logging).
 */
interface LogServiceInterface extends LoggerInterface
{
    /**
     * Registra un mensaje en el log.
     *
     * @param int|string $level Nivel del mensaje que se desea registrar.
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function log($level, $message, array $context = []): void;

    /**
     * Recupera los logs de la bitácora.
     *
     * Puede recuperar todos los logs o los de un nivel específico. Además
     * permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param int|string $level Nivel de los logs que se desean recuperar.
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getLogs(int $level = null, bool $newFirst = true): array;

    /**
     * Elimina los logs de un nivel específico de la bitácora.
     *
     * @param int|string $level Nivel de los logs que se desean limpiar.
     * @return void
     */
    public function clearLogs(?int $level = null): void;

    /**
     * Recupera los logs y los elimina de la bitácora.
     *
     * Puede recuperar todos los logs o los de un nivel específico. Además
     * permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param int|string $level Nivel de los logs que se desean recuperar.
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushLogs(int $level = null, bool $newFirst = true): array;

    /**
     * Registra un mensaje de nivel DEBUG en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function debug($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel INFO en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function info($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel NOTICE en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function notice($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel WARNING en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function warning($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel ERROR en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function error($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel CRITICAL en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function critical($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel ALERT en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function alert($message, array $context = []): void;

    /**
     * Registra un mensaje de nivel EMERGENCY en la bitácora.
     *
     * @param string $message El mensaje que se desea registrar.
     * @param array $context Contexto adicional para el mensaje.
     * @return void
     */
    public function emergency($message, array $context = []): void;

    /**
     * Recupera los logs de nivel DEBUG de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getDebugLogs(): array;

    /**
     * Recupera los logs de nivel INFO de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getInfoLogs(): array;

    /**
     * Recupera los logs de nivel NOTICE de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getNoticeLogs(): array;

    /**
     * Recupera los logs de nivel WARNING de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getWarningLogs(): array;

    /**
     * Recupera los logs de nivel ERROR de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getErrorLogs(): array;

    /**
     * Recupera los logs de nivel CRITICAL de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getCriticalLogs(): array;

    /**
     * Recupera los logs de nivel ALERT de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getAlertLogs(): array;

    /**
     * Recupera los logs de nivel EMERGENCY de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function getEmergencyLogs(): array;

    /**
     * Elimina los logs de nivel DEBUG de la bitácora.
     *
     * @return void
     */
    public function clearDebugLogs(): void;

    /**
     * Elimina los logs de nivel INFO de la bitácora.
     *
     * @return void
     */
    public function clearInfoLogs(): void;

    /**
     * Elimina los logs de nivel NOTICE de la bitácora.
     *
     * @return void
     */
    public function clearNoticeLogs(): void;

    /**
     * Elimina los logs de nivel WARNING de la bitácora.
     *
     * @return void
     */
    public function clearWarningLogs(): void;

    /**
     * Elimina los logs de nivel ERROR de la bitácora.
     *
     * @return void
     */
    public function clearErrorLogs(): void;

    /**
     * Elimina los logs de nivel CRITICAL de la bitácora.
     *
     * @return void
     */
    public function clearCriticalLogs(): void;

    /**
     * Elimina los logs de nivel ALERT de la bitácora.
     *
     * @return void
     */
    public function clearAlertLogs(): void;

    /**
     * Elimina los logs de nivel EMERGENCY de la bitácora.
     *
     * @return void
     */
    public function clearEmergencyLogs(): void;

    /**
     * Recupera los logs de nivel DEBUG y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushDebugLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel INFO y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushInfoLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel NOTICE y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushNoticeLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel WARNING y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushWarningLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel ERROR y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushErrorLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel CRITICAL y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushCriticalLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel ALERT y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushAlertLogs(bool $newFirst = true): array;

    /**
     * Recupera los logs de nivel EMERGENCY y los elimina de la bitácora.
     *
     * Permite entregar los logs en el orden que fueron ingresados o de más
     * nuevo a más antiguo.
     *
     * @param bool $newFirst Indica si se deben entregar logs nuevos primero.
     * @return array Arreglo con los logs solicitados.
     */
    public function flushEmergencyLogs(bool $newFirst = true): array;
}
