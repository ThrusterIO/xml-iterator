<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\Element;
use Thruster\Component\XMLIterator\ElementIterator;
use Thruster\Component\XMLIterator\XMLReader;

/**
 * Class ElementTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ElementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var XMLReader
     */
    protected $reader;

    protected function setUp()
    {
        $this->reader = new XMLReaderStub('<root><child pos="first">node value</child><child pos="first"/></root>');
    }

    public function testElementCreation()
    {
        $reader = $this->reader;
        $reader->next();
        $element = new Element($reader);
        $this->assertSame($element->getNodeTypeName(), $element->getNodeTypeName(XMLReader::ELEMENT));
        $this->assertSame($element->name, 'root');
    }

    public function testReaderAttributeHandling()
    {
        $reader = new XMLReaderStub("<root pos=\"first\" plue=\"a&#13;&#10;b&#32;  c\t&#9;d\">node value</root>");
        $reader->next();
        $this->assertSame("first", $reader->getAttribute('pos'));
        $this->assertSame("a\r\nb   c \td", $reader->getAttribute('plue'), 'entity handling');
        $element = new Element($reader);
        $xml     = $element->getXMLElementAround();
        $this->assertSame("<root pos=\"first\" plue=\"a&#13;&#10;b   c &#9;d\"/>", $xml, 'XML generation');
    }

    public function testCheckNodeValue()
    {
        $reader = new XMLReaderStub('<root><b>has</b></root>');
        /** @var ElementIterator|Node[] $it */
        $it    = new ElementIterator($reader);
        $count = 0;
        foreach ($it as $element) {
            $this->assertEquals('has', $element->readString());
            $count++;
        }
        $this->assertEquals(2, $count);
    }
}
