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

namespace Derafu\Lib\Core\Foundation\Abstract;

use Derafu\Lib\Core\Common\Trait\OptionsAwareTrait;
use Derafu\Lib\Core\Foundation\Contract\StrategyInterface;
use Derafu\Lib\Core\Foundation\Contract\WorkerInterface;
use Derafu\Lib\Core\Foundation\Exception\StrategyException;

/**
 * Clase base para los workers de la aplicación.
 */
abstract class AbstractWorker extends AbstractService implements WorkerInterface
{
    use OptionsAwareTrait;

    /**
     * Reglas de esquema del worker.
     *
     * El esquema se debe definir en cada worker. El formato del esquema es el
     * utilizado por Symfony\Component\OptionsResolver\OptionsResolver.
     *
     * @var array
     */
    protected array $optionsSchema = [];

    /**
     * Estrategias que el worker implementa.
     *
     * @var StrategyInterface[]
     */
    protected array $strategies;

    /**
     * Constructor del worker.
     *
     * @param array $strategies Estrategias que este worker implementa.
     */
    public function __construct(iterable $strategies = [])
    {
        $this->strategies = is_array($strategies)
            ? $strategies
            : iterator_to_array($strategies)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        $regex = "/\\\\Package\\\\([A-Za-z0-9_]+)\\\\Component\\\\([A-Za-z0-9_]+)\\\\Worker\\\\([A-Za-z0-9_]+)Worker/";

        $class = (string) $this;
        if (preg_match($regex, $class, $matches)) {
            return $matches[1] . ' ' . $matches[2] . ' ' . $matches[3];
        }

        return parent::getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getStrategy(string $strategy): StrategyInterface
    {
        if (!str_contains($strategy, '.')) {
            $strategy = 'default.' . $strategy;
        }

        if (!isset($this->strategies[$strategy])) {
            throw new StrategyException(sprintf(
                'No se encontró la estrategia %s en el worker %s (%s).',
                $strategy,
                $this->getName(),
                $this->getId(),
            ));
        }

        return $this->strategies[$strategy];
    }

    /**
     * {@inheritdoc}
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }
}
