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

namespace Derafu\Lib\Core\Package\Prime\Component\Template\Worker\Renderer\Strategy;

use Derafu\Lib\Core\Foundation\Abstract\AbstractStrategy;
use Derafu\Lib\Core\Helper\Arr;
use Derafu\Lib\Core\Package\Prime\Component\Template\Contract\Renderer\Strategy\MarkdownRendererStrategyInterface;
use Derafu\Lib\Core\Package\Prime\Component\Template\Exception\TemplateException;
use Embed\Embed;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\TableOfContents\TableOfContentsExtension;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension;
use League\CommonMark\Extension\Footnote\FootnoteExtension;
use League\CommonMark\Extension\DescriptionList\DescriptionListExtension;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\SmartPunct\SmartPunctExtension;
use League\CommonMark\Extension\ExternalLink\ExternalLinkExtension;
use League\CommonMark\Extension\FrontMatter\FrontMatterExtension;
use League\CommonMark\Extension\Mention\MentionExtension;
use League\CommonMark\Extension\Embed\EmbedExtension;
use League\CommonMark\Extension\Embed\Bridge\OscaroteroEmbedAdapter;
use League\CommonMark\Extension\FrontMatter\FrontMatterProviderInterface;

/**
 * Estrategia de renderizado de archivos Markdown (.md).
 */
class MarkdownRendererStrategy extends AbstractStrategy implements MarkdownRendererStrategyInterface
{
    /**
     * Instancia del convertidor de markdown.
     *
     * @var MarkdownConverter
     */
    private MarkdownConverter $markdown;

    /**
     * Configuración del ambiente de markdown.
     *
     * @var array
     */
    private array $config = [
        'extensions' => [
            CommonMarkCoreExtension::class,
            GithubFlavoredMarkdownExtension::class,
            TableOfContentsExtension::class,
            HeadingPermalinkExtension::class,
            FootnoteExtension::class,
            DescriptionListExtension::class,
            AttributesExtension::class,
            SmartPunctExtension::class,
            ExternalLinkExtension::class,
            FrontMatterExtension::class,
            MentionExtension::class,
            EmbedExtension::class,
        ],
        'environment' => [
            'table_of_contents' => [
                'min_heading_level' => 2,
                'max_heading_level' => 3,
                'normalize' => 'relative',
                'position' => 'placeholder',
                'placeholder' => '[TOC]',
            ],
            'heading_permalink' => [
                'html_class' => 'text-decoration-none small text-muted',
                'id_prefix' => 'content',
                'fragment_prefix' => 'content',
                'insert' => 'before',
                'title' => 'Permalink',
                'symbol' => '<i class="fa-solid fa-link"></i> ',
            ],
            'external_link' => [
                //'internal_hosts' => null, // Solo el Dominio (sin esquema HTTP).
                'open_in_new_window' => true,
                'html_class' => 'external-link',
                'nofollow' => 'external',
                'noopener' => 'external',
                'noreferrer' => 'external',
            ],
            'mentions' => [
                '@' => [
                    'prefix' => '@',
                    'pattern' => '[a-z\d](?:[a-z\d]|-(?=[a-z\d])){0,38}(?!\w)',
                    'generator' => 'https://github.com/%s',
                ],
                '#' => [
                    'prefix' => '#',
                    'pattern' => '\d+',
                    'generator' => "https://github.com/sowerphp/sowerphp-framework/issues/%d",
                ],
            ],
            'embed' => [
                //'adapter' => null, // new OscaroteroEmbedAdapter()
                'allowed_domains' => ['youtube.com'],
                'fallback' => 'link',
                'library' => [
                    'oembed:query_parameters' => [
                        'maxwidth' => 400,
                        'maxheight' => 300,
                    ],
                ],
            ],
        ],
    ];

    /**
     * Rutas donde están las plantillas.
     *
     * @var array
     */
    private array $paths;

    /**
     * Constructor de la estrategia.
     *
     * @param string|array $paths Rutas dónde se buscarán las plantillas.
     */
    public function __construct(string|array $paths = [])
    {
        $this->paths = is_string($paths) ? [$paths] : $paths;
    }

    /**
     * Renderiza una plantilla markdown y devuelve el resultado como una cadena.
     *
     * Además, si se ha solicitado, se entregará el contenido dentro de un
     * layout que se renderizará con PHP.
     *
     * @param string $template Plantilla markdown que se va a renderizar.
     * @param array $data Datos que se pasarán a la plantilla markdown para su
     * uso dentro de la vista.
     * @return string El contenido HTML generado por la plantilla markdown.
     */
    public function render(string $template, array &$data = []): string
    {
        // Cargar plantilla.
        $filepath = $this->resolveTemplate($template);
        $content = file_get_contents($filepath);

        // Obtener instancia del renderizador markdown (biblioteca externa).
        $config = $data['options']['config']['markdown'] ?? [];
        $markdown = $this->getMarkdown($config);

        // Reemplazar las variables del archivo Markdown con los datos.
        foreach ($data as $key => $value) {
            if (
                is_scalar($value)
                || (is_object($value) && method_exists($value, '__toString'))
            ) {
                $content = preg_replace(
                    '/\{\{\s*' . preg_quote($key, '/') . '\s*\}\}/',
                    $value,
                    $content
                );
            }
        }

        // Renderizar HTML a partir del contenido markdown.
        $result = $markdown->convert($content);
        $content = $result->getContent();

        // Reemplazos por diseño.
        $content = '<div class="markdown-body">' . $content . '</div>';
        $content = str_replace(
            [
                htmlspecialchars(
                    $this->config['environment']['heading_permalink']['symbol']
                ),
            ],
            [
                $this->config['environment']['heading_permalink']['symbol'],
            ],
            $content
        );

        // Acceder a los metadatos del Front Matter para agregar variables que
        // se hayan definido a $data y se puedan utilizar en el worker.
        if ($result instanceof FrontMatterProviderInterface) {
            $frontMatter = $result->getFrontMatter();
            $data = array_merge($data, $frontMatter);
        }

        // Entregar el contenido renderizado.
        return $content;
    }

    /**
     * Entrega la instancia del renderizador markdown.
     *
     * @param array $config
     * @return MarkdownConverter
     */
    private function getMarkdown(array $config): MarkdownConverter
    {
        if (!isset($this->markdown)) {
            // Cargar configuración del motor de renderizado.
            $this->config = $this->loadConfigurations($config);

            // Crear ambiente (entorno).
            $environment = new Environment($this->config['environment']);

            // Agregar extensiones.
            foreach ($this->config['extensions'] as $extension) {
                $environment->addExtension(new $extension());
            }

            // Crear instancia del convertidor de markdown.
            $this->markdown = new MarkdownConverter($environment);
        }

        return $this->markdown;
    }

    /**
     * Genera la configuración para el ambiente de conversión de markdown.
     *
     * @param array $config Configuración que se unirá a la por defecto.
     * @return array
     */
    private function loadConfigurations(array $config = []): array
    {
        // Configuración de 'external_link'.
        // Se hace antes del merge de abajo por si se desea sobrescribir
        // mediante la configuración de la aplicación (no debería).
        // $config['environment']['external_link']['internal_hosts'] = '';

        // Armar configuración usando la por defecto y la de la aplicación
        $config = Arr::mergeRecursiveDistinct(Arr::mergeRecursiveDistinct(
            $this->config,
            $this->getOptions()->all()
        ), $config);

        // Configuración de 'embed'.
        $embedLibrary = new Embed();
        $embedLibrary->setSettings($config['environment']['embed']['library']);
        $config['environment']['embed']['adapter'] =
            new OscaroteroEmbedAdapter($embedLibrary)
        ;
        unset($config['environment']['embed']['library']);

        // Entregar la configuración que se cargó.
        return $config;
    }

    /**
     * Resuelve la plantilla que se está solicitando.
     *
     * Se encarga de:
     *
     *   - Agregar la extensión a la plantilla.
     *   - Entregar la plantilla si es una ruta absoluta.
     *   - Buscar la plantilla en las rutas.
     *
     * @param string $template
     * @return string
     */
    private function resolveTemplate(string $template): string
    {
        // Agregar extensión.
        if (!str_ends_with($template, '.md')) {
            $template .= '.md';
        }

        // Si se pasó una ruta absoluta se entrega directamente.
        if ($template[0] === '/') {
            if (!file_exists($template)) {
                throw new TemplateException(sprintf(
                    'La plantilla %s no fue encontrada.',
                    $template
                ));
            }

            return $template;
        }

        // Buscar la plantilla en diferentes rutas.
        foreach ($this->paths as $path) {
            if (file_exists($path . '/' . $template)) {
                return $path . '/' . $template;
            }
        }

        // No se encontró la plantilla.
        throw new TemplateException(sprintf(
            'La plantilla %s no fue encontrada. Se buscó en los directorios: %s',
            $template,
            implode(' ', $this->paths)
        ));
    }
}
