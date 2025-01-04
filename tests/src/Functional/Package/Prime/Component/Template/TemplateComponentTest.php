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

namespace Derafu\Lib\Tests\Functional\Package\Prime\Component\Certificate;

use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\RendererWorkerInterface;
use Derafu\Lib\Core\Package\Prime\Component\Template\TemplateComponent;
use Derafu\Lib\Core\Package\Prime\Component\Template\Worker\RendererWorker;
use Derafu\Lib\Tests\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TemplateComponent::class)]
#[CoversClass(RendererWorker::class)]
class TemplateComponentTest extends TestCase
{
    private RendererWorkerInterface $renderer;

    public function setUp(): void
    {
        $this->renderer = new RendererWorker();
    }

    public function testRenderCustomTemplate()
    {
        $testsPath = realpath(dirname(__DIR__, 6));
        $template = $testsPath . '/fixtures/package/prime/template/custom_template';
        $data = [
            'title' => 'Derafu',
            'content' => 'I Love Derafu <3',
        ];
        $pdf = $this->renderer->render($template, $data);

        $this->assertIsString($pdf);
    }
}
