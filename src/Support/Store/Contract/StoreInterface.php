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

namespace Derafu\Lib\Core\Support\Store\Contract;

/**
 * Interfaz base para todos los almacenamientos.
 */
interface StoreInterface
{
    /**
     * Obtiene todos los valores almacenados.
     *
     * @return array Arreglo con todos los valores almacenados.
     */
    public function all(): array;

    /**
     * Asigna un valor a una llave.
     *
     * @param string $key Llave donde se almacenará el valor.
     * @param mixed $value Valor que se desea almacenar.
     * @return static Permite encadenar métodos.
     */
    public function set(string $key, mixed $value): static;

    /**
     * Obtiene un valor almacenado.
     *
     * @param string $key Llave del valor que se desea obtener.
     * @param mixed $default Valor por defecto si la llave no existe.
     * @return mixed Valor almacenado o valor por defecto.
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * Verifica si existe un valor para una llave.
     *
     * @param string $key Llave que se desea verificar.
     * @return bool True si la llave existe, false en caso contrario.
     */
    public function has(string $key): bool;

    /**
     * Elimina todos los valores almacenados.
     */
    public function clear(): void;
}