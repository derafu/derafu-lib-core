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

namespace Derafu\Lib\Core\Package\Prime\Component\Template\Service;

use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\DataFormatterInterface;
use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\DataHandlerInterface;
use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\DataInterface;
use Derafu\Lib\Core\Package\Prime\Component\Template\Entity\Data;
use Derafu\Lib\Core\Support\Store\Contract\RepositoryInterface;

/**
 * Servicio de formateo de datos.
 *
 * Permite recibir un valor y formatearlo según un mapa de handlers predefinido
 * mediante su identificador.
 */
class DataFormatter implements DataFormatterInterface
{
    /**
     * Mapeo de identificadores a la forma que se usará para darle formato a los
     * valores asociados al identificador.
     *
     * @var array<string,string|array|callable|DataHandlerInterface|RepositoryInterface>
     */
    private array $handlers;

    /**
     * Handler por defecto de los formatos.
     *
     * @var DataHandlerInterface
     */
    private DataHandlerInterface $handler;

    /**
     * Constructor del servicio.
     *
     * @param array $handlers
     */
    public function __construct(array $handlers = [])
    {
        $this->setHandlers($handlers);
        $this->handler = new DataHandler();
    }

    /**
     * @inheritDoc
     */
    public function setHandlers(array $handlers): static
    {
        $this->handlers = $handlers;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @inheritDoc
     */
    public function addHandler(
        string $id,
        string|array|callable|DataHandlerInterface|RepositoryInterface $handler
    ): static {
        $this->handlers[$id] = $handler;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getHandler(string $id): string|array|callable|DataHandlerInterface|RepositoryInterface|null
    {
        return $this->handlers[$id] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function format(string $id, mixed $value): string
    {
        // Si no hay handler exacto revisar si el ID tiene partes.
        if (!isset($this->handlers[$id])) {
            // Si el ID tiene partes se busca si la primera parte está definida
            // como handler.
            if (str_contains($id, '.')) {
                // Separar en handler e ID y si existe el formato se usa.
                [$handler, $id] = explode('.', $id, 2);
                if (isset($this->handlers[$handler])) {
                    return $this->handle($handler, $id, $value);
                }
            }
        }
        // El ID es el formato.
        else {
            return $this->handle($id, $id, $value);
        }

        // Buscar si hay un handler genérico (comodín).
        if (!isset($this->handlers['*'])) {
            return $this->handle('*', $id, $value);
        }

        // Si no hay handler para manejar se retorna como string el valor
        // original que se pasó casteado a string (lo que podría fallar).
        return (string) $value;
    }

    /**
     * Maneja el formateao de los datos según cierto handler.
     *
     * @param string $name Nombre del handler registrado que se debe utilizar.
     * @param string $id Identificador pasado del formato.
     * @param mixed $value Valor a formatear.
     * @return string Valor formateado.
     */
    private function handle(string $name, string $id, mixed $value): string
    {
        $handler = $this->handlers[$name];
        $data = $this->createDataInstance($id, $value);

        if ($handler instanceof DataHandlerInterface) {
            $handler->handle($data);
        } else {
            $this->handler->handle($data);
        }

        return $data->getFormatted();
    }

    /**
     * Crea una instancia de los datos que se requiere formatear.
     *
     * @param string $id
     * @param mixed $value
     * @return DataInterface
     */
    protected function createDataInstance(string $id, mixed $value): DataInterface
    {
        return new Data($id, $value);
    }
}
