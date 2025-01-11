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
use Derafu\Lib\Core\Support\Store\Contract\RepositoryInterface;

/**
 * Servicio de formateo de datos.
 *
 * Permite recibir un valor y formatearlo según un mapa de formateos predefinido
 * mediante su identificador.
 */
class DataFormatter implements DataFormatterInterface
{
    /**
     * Mapeo de identificadores a la forma que se usará para darle formato a los
     * valores asociados al identificador.
     *
     * @var array<string,string|array|callable|RepositoryInterface>
     */
    private array $formats;

    /**
     * Constructor del servicio.
     *
     * @param array $formats
     */
    public function __construct(array $formats = [])
    {
        $this->setFormats($formats);
    }

    /**
     * @inheritDoc
     */
    public function setFormats(array $formats): static
    {
        $this->formats = $formats;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @inheritDoc
     */
    public function addFormat(
        string $id,
        string|array|callable|RepositoryInterface $format
    ): static {
        $this->formats[$id] = $format;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function format(string $id, mixed $value): string
    {
        // Si no hay formato, devolver como string el valor pasado.
        if (!isset($this->formats[$id])) {
            return (string) $value;
        }

        // Obtener el formato que se debe utilizar.
        $format = $this->formats[$id];

        // Si es un string es una máscara de sprint.
        if (is_string($format)) {
            return sprintf($format, $value);
        }

        // Si es un arreglo es el arreglo deberá contener el valor a traducir.
        // Si no existe, se entregará el mismo valor como string.
        if (is_array($format)) {
            return $format[$value] ?? (string) $value;
        }

        // Si es una función se llama directamente y se retorna su resultado.
        if (is_callable($format)) {
            return $format($value);
        }

        // Si es un repositorio se busca la entidad y se retorna el string que
        // representa la interfaz. Cada Entidad deberá implementar __toString().
        if ($format instanceof RepositoryInterface) {
            $entity = $format->find($value);
            return $entity->__toString();
        }
    }
}
