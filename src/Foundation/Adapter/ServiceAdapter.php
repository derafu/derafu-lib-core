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

namespace Derafu\Lib\Core\Foundation\Adapter;

use Derafu\Lib\Core\Foundation\Contract\ServiceInterface;

/**
 * Permite proporcionar de manera segura un servicio que no implementa la
 * interfaz ServiceInterface.
 *
 * El adaptador permite usar de manera segura el servicio. Exponiendo solo los
 * métodos públicos que la instancia de la clase adaptada tenga. No se permite
 * el acceso directo a atributos de la instancia adaptada.
 *
 * Este adaptador es para usos muy específicos, se desaconseja su uso y en
 * general siempre debe construirse un servicio que implemente ServiceInterface
 * o extienda de AbstractService utilizando composición.
 */

/**
 * Clase que funciona como envoltura de una instancia.
 *
 * Es un adaptador que permite usar cualquier método público de la instancia
 * como un servicio.
 */
class ServiceAdapter implements ServiceInterface
{
    /**
     * Instancia que se está adaptando a servicio.
     *
     * @var object
     */
    private object $adaptee;

    /**
     * Constructor del adaptador.
     *
     * @param object $adaptee
     */
    public function __construct(object $adaptee)
    {
        $this->adaptee = $adaptee;
    }

    /**
     * Método mágico para poder llamar a cualquier método público de la
     * instancia de la clase adaptada a través del servicio.
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        return call_user_func_array([$this->adaptee, $name], $arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return (string) $this->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): int|string
    {
        if (method_exists($this->adaptee, 'getId')) {
            return $this->adaptee->getId();
        }

        return get_class($this->adaptee);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        if (method_exists($this->adaptee, 'getName')) {
            return $this->adaptee->getName();
        }

        return get_class($this->adaptee);
    }
}
