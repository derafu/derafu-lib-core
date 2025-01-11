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

namespace Derafu\Lib\Core\Package\Prime\Component\Template\Contract;

use Derafu\Lib\Core\Support\Store\Contract\RepositoryInterface;

/**
 * Interfaz para la clase que formatea los valores de datos.
 */
interface DataFormatterInterface
{
    /**
     * Asigna el mapa de formatos.
     *
     * Reemplazará un mapa previo si existía.
     *
     * @param array $formats
     * @return static
     */
    public function setFormats(array $formats): static;

    /**
     * Obtiene el mapa de formatos.
     *
     * @return array<string,string|array|callable|RepositoryInterface>
     */
    public function getFormats(): array;

    /**
     * Agrega un formato al mapa de formatos.
     *
     * @param string $id
     * @param string|array|callable|RepositoryInterface $format
     * @return static
     */
    public function addFormat(
        string $id,
        string|array|callable|RepositoryInterface $format
    ): static;

    /**
     * Formatea el valor de un identificador según el mapa de formatos que esté
     * disponible en el servicio.
     *
     * @param string $id
     * @param mixed $value
     * @return string
     */
    public function format(string $id, mixed $value): string;
}
