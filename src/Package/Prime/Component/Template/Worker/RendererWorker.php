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

namespace Derafu\Lib\Core\Package\Prime\Component\Template\Worker;

use Derafu\Lib\Core\Foundation\Abstract\AbstractWorker;
use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\RendererWorkerInterface;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Worker de renderización de plantillas.
 *
 * Permite renderizar plantillas Twig a HTML y PDF.
 *
 * TODO: Ordenar código, usar configuraciones/opciones y separar en estrategias.
 */
class RendererWorker extends AbstractWorker implements RendererWorkerInterface
{
    /**
     * Formato por defecto que se debe utilizar.
     *
     * @var string
     */
    private string $defaultFormat = 'pdf';

    /**
     * Rutas donde están las plantillas.
     *
     * @var array
     */
    private array $paths = [];

    /**
     * Cargador de plantillas mediante el sistema de archivos para Twig.
     */
    private FilesystemLoader $filesystemLoader;

    /**
     * Renderizador de plantillas HTML con Twig.
     *
     * @var Environment
     */
    private Environment $twig;

    /**
     * Constructor del worker.
     *
     * @param string|array $paths Rutas dónde se buscarán las plantillas.
     */
    public function __construct(string|array $paths = [])
    {
        $this->paths = is_string($paths) ? [$paths] : $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function render(string $template, array $data = []): string
    {
        // Formato en el que se renderizará la plantilla.
        $format = $data['options']['format'] ?? $this->defaultFormat;

        // Renderizar HTML de la plantilla Twig.
        $html = $this->renderHtml($template, $data);

        // Si el formato solicitado es HTML se retorna directamente.
        if ($format === 'html') {
            return $html;
        }

        // Renderizar el PDF a partir del HTML.
        $configPdf = $data['options']['config']['pdf'] ?? [];
        $pdf = $this->renderPdf($html, $configPdf);

        // Entregar el contenido del PDF renderizado.
        return $pdf;
    }

    /**
     * Entrega la instancia de Twig.
     *
     * Este método evita crearla en el constructor y se crea solo cuando
     * realmente se utiliza. Útil porque los workers son lazy services.
     *
     * @return Environment
     */
    private function getTwig(): Environment
    {
        if (!isset($this->twig)) {
            $this->filesystemLoader = new FilesystemLoader($this->paths);
            $this->twig = new Environment($this->filesystemLoader);
        }

        return $this->twig;
    }

    /**
     * Renderiza una plantilla Twig en HTML.
     *
     * @param string $template Plantilla Twig a renderizar.
     * @param array $data Datos que se pasarán a la plantilla Twig.
     * @return string Código HTML con el renderizado de la plantilla Twig.
     */
    private function renderHtml(string $template, array $data): string
    {
        // Resolver plantilla.
        $template = $this->resolveTemplate($template);

        // Renderizar la plantilla.
        return $this->getTwig()->render($template, $data);
    }

    /**
     * Renderiza un HTML en un documento PDF.
     *
     * El renderizado se realiza a partir de un HTML previamente renderizado que
     * será pasado a PDF.
     */
    private function renderPdf(string $html, array $config): string
    {
        // Generar el PDF con mPDF.
        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        // Obtener el contenido del PDF.
        $pdf = $mpdf->Output('', Destination::STRING_RETURN);

        // Entregar el contenido del PDF.
        return $pdf;
    }

    /**
     * Resuelve la plantilla que se está solicitando.
     *
     * Se encarga de:
     *
     *   - Agregar la extensión a la plantilla.
     *   - Agregar el directorio si se pasó una ruta absoluta de la plantilla.
     *
     * @param string $template
     * @return string
     */
    private function resolveTemplate(string $template): string
    {
        // Agregar extensión.
        if (!str_ends_with($template, '.html.twig')) {
            $template .= '.html.twig';
        }

        // Agregar el directorio si se pasó una ruta absoluta.
        if ($template[0] === '/') {
            $dir = dirname($template);
            $this->filesystemLoader->addPath($dir);
            $template = basename($template);
        }

        // Entregar nombre de la plantilla.
        return $template;
    }
}
