<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\ElementIterator;
use Thruster\Component\XMLIterator\Node;
use Thruster\Component\XMLIterator\XMLReader;

/**
 * Class NodeTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class NodeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * some XMLReaderNode can not be turned into a SimpleXMLElement, this tests how robust XMLReaderNode
     * is for the job.
     */
    public function testAsSimpleXMLforElementAndSignificantWhitespace()
    {
        $reader = new XMLReaderStub('<root>
            <!-- <3 <3 love XMLReader::SIGNIFICANT_WHITESPACE (14) <3 <3 -->
        </root>');

        $reader->read(); // (#1) <root>

        // test asSimpleXML() for XMLReader::ELEMENT
        $this->assertSame(XMLReader::ELEMENT, $reader->nodeType);
        $node = new Node($reader);
        $sxml = $node->getSimpleXMLElement();
        $this->assertInstanceOf('SimpleXMLElement', $sxml);

        $reader->read(); // (#14) SIGNIFICANT_WHITESPACE

        // test asSimpleXML() for XMLReader::SIGNIFICANT_WHITESPACE
        $this->assertSame(XMLReader::SIGNIFICANT_WHITESPACE, $reader->nodeType);
        $node = new Node($reader);
        $sxml = $node->getSimpleXMLElement();
        $this->assertNull($sxml);
    }

    public function testXxpand()
    {
        $reader = new XMLReaderStub('<products>
            <!--suppress HtmlUnknownAttribute -->
            <product category="Desktop">
                <name> Desktop 1 (d)</name>
                <price>499.99</price>
            </product>
            <!--suppress HtmlUnknownAttribute -->
            <product category="Tablet">
                <name>Tablet 1 (t)</name>
                <price>1099.99</price>
            </product>
        </products>');

        $products = new ElementIterator($reader, 'product');
        $doc      = new \DOMDocument();
        $xpath    = new \DOMXPath($doc);
        foreach ($products as $product) {
            $node = $product->expand($doc);
            $this->assertInstanceOf('DOMNode', $node);
            $this->assertSame($node->ownerDocument, $doc);
            $this->assertEquals('product', $xpath->evaluate('local-name(.)', $node));
            $this->addToAssertionCount(1);
        }
        $this->assertGreaterThan(0, $previous = $this->getNumAssertions());

        unset($doc);
        $reader->rewind();
        foreach ($products as $product) {
            $node = $product->expand();
            $this->assertInstanceOf('DOMNode', $node);
            $this->assertInstanceOf('DOMDocument', $node->ownerDocument);
            $doc   = $node->ownerDocument;
            $xpath = new \DOMXPath($doc);
            $this->assertSame($node->ownerDocument, $doc);
            $this->assertEquals('product', $xpath->evaluate('local-name(.)', $node));
            $this->addToAssertionCount(1);
        }

        $this->assertGreaterThan($previous, $this->getNumAssertions());
    }
}
