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

namespace Derafu\Lib\Core\Package\Prime\Component\Xml\Contract;

use Derafu\Lib\Core\Package\Prime\Component\Xml\Exception\XmlException;
use DOMElement;
use DOMNode;
use DOMParentNode;

/**
 * Interfaz para la clase que representa un documento XML.
 *
 * @see DOMDocument
 */
interface XmlInterface extends DOMParentNode
{
    /**
     * Entrega el nombre del tag raíz del XML.
     *
     * @return string Nombre del tag raíz.
     */
    public function getName(): string;

    /**
     * Obtiene el espacio de nombres (namespace) del elemento raíz del
     * documento XML.
     *
     * @return string|null Espacio de nombres del documento XML o `null` si no
     * está presente.
     */
    public function getNamespace(): ?string;

    /**
     * Entrega el nombre del archivo del schema del XML.
     *
     * @return string|null Nombre del schema o `null` si no se encontró.
     */
    public function getSchema(): ?string;

    /**
     * Carga un string XML en la instancia del documento XML.
     *
     * @param string $source String con el documento XML a cargar.
     * @param int $options Opciones para la carga del XML.
     * @return bool `true` si el XML se cargó correctamente.
     * @throws XmlException Si no es posible cargar el XML.
     */
    public function loadXml(string $source, int $options = 0): bool;

    /**
     * Genera el documento XML como string.
     *
     * Wrapper de parent::saveXml() para poder corregir XML entities.
     *
     * Incluye encabezado del XML con versión y codificación.
     *
     * @param DOMNode|null $node Nodo a serializar.
     * @param int $options Opciones de serialización.
     * @return string XML serializado y corregido.
     */
    public function saveXml(?DOMNode $node = null, int $options = 0): string;

    /**
     * Genera el documento XML como string.
     *
     * Wrapper de saveXml() para generar un string sin el encabezado del XML y
     * sin salto de línea inicial o final.
     *
     * @return string XML serializado y corregido.
     */
    public function getXml(): string;

    /**
     * Entrega el string XML canonicalizado y con la codificación que
     * corresponde (ISO-8859-1).
     *
     * Esto básicamente usa C14N(), sin embargo, C14N() siempre entrega el XML
     * en codificación UTF-8. Por lo que este método permite obtenerlo con C14N
     * pero con la codificación correcta de ISO-8859-1. Además se corrigen las
     * XML entities.
     *
     * @param string|null $xpath XPath para consulta al XML y extraer solo una
     * parte, desde un tag/nodo específico.
     * @return string String XML canonicalizado.
     * @throws XmlException En caso de ser pasado un XPath y no encontrarlo.
     */
    public function C14NWithIsoEncoding(?string $xpath = null): string;

    /**
     * Entrega el string XML canonicalizado, con la codificación que
     * corresponde (ISO-8859-1) y aplanado.
     *
     * Es un wrapper de C14NWithIsoEncoding() que aplana el XML resultante.
     *
     * @param string|null $xpath XPath para consulta al XML y extraer solo una
     * parte, desde un tag/nodo específico.
     * @return string String XML canonicalizado y aplanado.
     * @throws XmlException En caso de ser pasado un XPath y no encontrarlo.
     */
    public function C14NWithIsoEncodingFlattened(?string $xpath = null): string;

    /**
     * Obtiene el string del nodo de la firma electrónica del XML.
     *
     * @return string|null String XML de la firma si existe.
     */
    public function getSignatureNodeXml(): ?string;

    /**
     * Ejecuta una consulta XPath sobre el documento XML.
     *
     * La consulta que se realiza es sencilla, sin namespaces. Si se requiere el
     * uso de namespace se debe usar directamente la clase XPathQuery.
     *
     * @param string $query Consulta XPath con marcadores nombrados (ej.: ":param").
     * @param array $params Arreglo de parámetros en formato ['param' => 'value'].
     * @return string|array|null
     */
    public function query(string $query, array $params = []): string|array|null;

    /**
     * Entrega la instancia del elemento raíz del documento.
     *
     * Esta es una forma segura de acceder a $documentElement cuando se usa la
     * referencia a esta interfaz como tipo de datos de la variable.
     *
     * Si no se usa esto, herramientas como phpstan reclamarán.
     *
     * @return DOMElement|null
     */
    public function getDocumentElement(): ?DOMElement;

    /**
     * Canonicaliza el XML de acuerdo con la especificación C14N.
     *
     * La implementación oficial de esta interfaz hereda de DOMDocument por lo
     * que este método está disponible. Sin embargo se define en la interfaz
     * para que herramientas como phpstan no reclamen cuando se usa la
     * referencia a esta interfaz como tipo de datos de la variable.
     *
     * @param bool $exclusive Indica si se utiliza la canonicalización
     * exclusiva.
     * @param bool $withComments Incluye los comentarios en la salida si es
     * `true`.
     * @param array|null $xpath Lista opcional de nodos para incluir en la
     * canonicalización.
     * @param array|null $nsPrefixes Prefijos de namespaces a considerar en la
     * canonicalización.
     * @return string|false Una cadena con el XML canonicalizado o `false` en
     * caso de error.
     */
    public function C14N(
        bool $exclusive = false,
        bool $withComments = false,
        ?array $xpath = null,
        ?array $nsPrefixes = null
    ): string|false;

    /**
     * Crea un nuevo elemento en el documento XML.
     *
     * La implementación oficial de esta interfaz hereda de DOMDocument por lo
     * que este método está disponible. Sin embargo se define en la interfaz
     * para que herramientas como phpstan no reclamen cuando se usa la
     * referencia a esta interfaz como tipo de datos de la variable.
     *
     * @param string $localName El nombre local del elemento.
     * @param string $value El valor opcional del elemento.
     * @return DOMElement|false El elemento creado o `false` en caso de error.
     */
    public function createElement(
        string $localName,
        string $value = ''
    ); // Agregar tipo de retorno es incompatible con DOMDocument oficial de PHP.

    /**
     * Crea un nuevo elemento en un namespace específico.
     *
     * La implementación oficial de esta interfaz hereda de DOMDocument por lo
     * que este método está disponible. Sin embargo se define en la interfaz
     * para que herramientas como phpstan no reclamen cuando se usa la
     * referencia a esta interfaz como tipo de datos de la variable.
     *
     * @param string|null $namespace URI del namespace (puede ser `null` para
     * ninguno).
     * @param string $qualifiedName Nombre calificado del elemento.
     * @param string $value Valor opcional del elemento.
     * @return DOMElement|false El elemento creado o `false` en caso de error.
     */
    public function createElementNS(
        ?string $namespace,
        string $qualifiedName,
        string $value = ''
    ); // Agregar tipo de retorno es incompatible con DOMDocument oficial de PHP.

    /**
     * Valida el documento XML actual contra un esquema XML.
     *
     * La implementación oficial de esta interfaz hereda de DOMDocument por lo
     * que este método está disponible. Sin embargo se define en la interfaz
     * para que herramientas como phpstan no reclamen cuando se usa la
     * referencia a esta interfaz como tipo de datos de la variable.
     *
     * @param string $filename Ruta al archivo del esquema (.xsd).
     * @param int $flags Opciones de validación (por defecto `0`).
     * @return bool `true` si la validación es exitosa, `false` en caso de error.
     */
    public function schemaValidate(string $filename, int $flags = 0): bool;
}
