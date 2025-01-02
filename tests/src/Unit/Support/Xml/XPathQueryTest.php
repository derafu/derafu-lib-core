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

namespace Derafu\Lib\Tests\Unit\Support\Store;

use Derafu\Lib\Core\Support\Xml\XPathQuery;
use Derafu\Lib\Tests\TestCase;
use DOMDocument;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(XPathQuery::class)]
class XPathQueryTest extends TestCase
{
    private string $validXml;

    private string $invalidXml;

    protected function setUp(): void
    {
        $this->validXml = <<<XML
            <root>
                <item id="1">First</item>
                <item id="2">Second</item>
                <item id="3">Third</item>
            </root>
        XML;

        $this->invalidXml = <<<XML
            <root>
                <item id="1">First</item>
                <item id="2">Second</item>
                <!-- Missing closing tag for root -->
        XML;
    }

    public function testGetSingleValue(): void
    {
        $query = new XPathQuery($this->validXml);
        $result = $query->getValue('/root/item[@id="1"]');

        $this->assertSame('First', $result);
    }

    public function testGetMultipleValues(): void
    {
        $query = new XPathQuery($this->validXml);
        $results = $query->getValues('/root/item');

        $this->assertCount(3, $results);
        $this->assertSame(['First', 'Second', 'Third'], $results);
    }

    public function testGetNodes(): void
    {
        $query = new XPathQuery($this->validXml);
        $nodes = $query->getNodes('/root/item');

        $this->assertSame(3, $nodes->length);
        $this->assertSame('First', $nodes->item(0)->nodeValue);
        $this->assertSame('Second', $nodes->item(1)->nodeValue);
        $this->assertSame('Third', $nodes->item(2)->nodeValue);
    }

    public function testGetSingleResultWithGet(): void
    {
        $query = new XPathQuery($this->validXml);
        $result = $query->get('/root/item[@id="2"]');

        $this->assertSame('Second', $result);
    }

    public function testGetMultipleResultsWithGet(): void
    {
        $query = new XPathQuery($this->validXml);
        $results = $query->get('/root/item');

        $this->assertSame(['First', 'Second', 'Third'], $results);
    }

    public function testGetNullForNoMatch(): void
    {
        $query = new XPathQuery($this->validXml);
        $result = $query->get('/root/nonexistent');

        $this->assertNull($result);
    }

    public function testInvalidXPathThrowsException(): void
    {
        $query = new XPathQuery($this->validXml);

        $this->expectException(InvalidArgumentException::class);
        $query->getNodes('//root@invalid_xpath]');
    }

    public function testLoadMalformedXmlThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new XPathQuery($this->invalidXml);
    }

    public function testGetDomDocument(): void
    {
        $query = new XPathQuery($this->validXml);
        $dom = $query->getDomDocument();

        $this->assertInstanceOf(DOMDocument::class, $dom);
        $this->assertSame('root', $dom->documentElement->nodeName);
    }
}
