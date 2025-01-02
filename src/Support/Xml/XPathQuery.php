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

namespace Derafu\Lib\Core\Support\Xml;

use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use InvalidArgumentException;

/**
 * Clase para facilitar el manejo de XML usando XPath.
 */
class XPathQuery
{
    /**
     * Instancia del documento XML.
     *
     * @var DOMDocument
     */
    private readonly DOMDocument $dom;

    /**
     * Instancia que representa el buscador con XPath.
     *
     * @var DOMXPath
     */
    private readonly DOMXPath $xpath;

    /**
     * Constructor que recibe el documento XML y prepara XPath.
     *
     * @param string|DOMDocument $xml Documento XML.
     */
    public function __construct(string|DOMDocument $xml)
    {
        if ($xml instanceof DOMDocument) {
            $this->dom = $xml;
        } else {
            $this->dom = new DOMDocument();
            $this->loadXml($xml);
        }

        $this->xpath = new DOMXPath($this->dom);
    }

    /**
     * Carga un string XML en el atributo $dom.
     *
     * @param string $xml
     * @return static
     */
    private function loadXml(string $xml): static
    {
        $use_errors = libxml_use_internal_errors(true);

        $this->dom->loadXml($xml);

        if ($error = libxml_get_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'El XML proporcionado no es válido: %s.',
                $error->message
            ));
        }

        libxml_clear_errors();
        libxml_use_internal_errors($use_errors);

        return $this;
    }

    /**
     * Devuelve el DOMDocument usado internamente.
     *
     * @return DOMDocument
     */
    public function getDomDocument(): DOMDocument
    {
        return $this->dom;
    }

    /**
     * Ejecuta una consulta XPath y devuelve el resultado procesado.
     *
     * El resultado dependerá de o que se encuentre:
     *
     *   - `null`: si no hubo coincidencias.
     *   - string: si hubo una coincidencia.
     *   - string[]: si hubo más de una coincidencia.
     *
     * Si el nodo tiene hijos, devuelve un arreglo recursivo representando
     * toda la estructura de los nodos.
     *
     * @param string $query Consulta XPath.
     * @return string|string[]|null El valor procesado: string, arreglo o null.
     */
    public function get(string $query): string|array|null
    {
        $nodes = $this->getNodes($query);

        // Sin coincidencias.
        if ($nodes->length === 0) {
            return null;
        }

        // Un solo nodo.
        if ($nodes->length === 1) {
            return $this->processNode($nodes->item(0));
        }

        // Varios nodos.
        $results = [];
        foreach ($nodes as $node) {
            $results[] = $this->processNode($node);
        }

        return $results;
    }

    /**
     * Procesa un nodo DOM y sus hijos recursivamente.
     *
     * @param DOMNode $node Nodo DOM a procesar.
     * @return string|array Valor del nodo o estructura de hijos como arreglo.
     */
    private function processNode(DOMNode $node): string|array
    {
        if ($node->hasChildNodes()) {
            $children = [];
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $children[$child->nodeName] = $this->processNode($child);
                }
            }

            // Si tiene hijos procesados, devolver la estructura.
            return count($children) > 0 ? $children : $node->nodeValue;
        }

        // Si no tiene hijos, devolver el valor.
        return $node->nodeValue;
    }


    /*public function get(string $query): string|array|null
    {
        // Ejecutar consulta.
        $results = $this->getValues($query);

        // Si no hay resultados null.
        if (!isset($results[0])) {
            return null;
        }

        // Si solo hay un resultado un string.
        if (!isset($results[1])) {
            return $results[0];
        }

        // Más de un resultado, arreglo.
        return $results;
    }*/

    /**
     * Ejecuta una consulta XPath y devuelve un arreglo de valores.
     *
     * @param string $query Consulta XPath.
     * @return string[] Arreglo de valores encontrados.
     */
    public function getValues(string $query): array
    {
        $nodes = $this->getNodes($query);

        $results = [];
        foreach ($nodes as $node) {
            $results[] = $node->nodeValue;
        }

        return $results;
    }

    /**
     * Ejecuta una consulta XPath y devuelve el primer resultado como string.
     *
     * @param string $query Consulta XPath.
     * @return string|null El valor del nodo, o `null` si no existe.
     */
    public function getValue(string $query): ?string
    {
        $nodes = $this->getNodes($query);

        return $nodes->length > 0
            ? $nodes->item(0)->nodeValue
            : null
        ;
    }

    /**
     * Ejecuta una consulta XPath y devuelve los nodos resultantes.
     *
     * @param string $query Consulta XPath.
     * @return DOMNodeList Nodos resultantes de la consulta XPath.
     */
    public function getNodes(string $query): DOMNodeList
    {
        $use_errors = libxml_use_internal_errors(true);

        $nodes = $this->xpath->query($query);

        if ($nodes === false || $error = libxml_get_last_error()) {
            throw new InvalidArgumentException(sprintf(
                'Ocurrió un error al ejecutar la expresión XPath: %s.',
                $query
            ));
        }

        libxml_clear_errors();
        libxml_use_internal_errors($use_errors);

        return $nodes;
    }
}
