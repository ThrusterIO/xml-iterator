<?php

namespace Thruster\Component\XMLIterator\Tests;

use SebastianBergmann\CodeCoverage\Report\Xml\Node;
use Thruster\Component\XMLIterator\ElementIterator;
use Thruster\Component\XMLIterator\XMLReader;

/**
 * Class ElementIteratorTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ElementIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationAndCurrent()
    {
        $reader = $this->createReader();

        $it = new ElementIterator($reader);

        $this->assertSame('xml', $it->current()->getName());
        $it->next();
        $this->assertSame('node1', $it->current()->getName());
        $it->next();
        $this->assertSame('info1', $it->current()->getName());
    }

    /** @test */
    public function string()
    {
        $reader = new XMLReaderStub('<root><b>has</b></root>');

        /** @var ElementIterator|Node[]|XMLReader $it */
        $it = new ElementIterator($reader);

        $it->rewind();
        $this->assertEquals(true, $it->valid());
        $this->assertEquals("has", (string) $it);
        $this->assertEquals("has", $it->readString());
    }

    /** @test */
    public function iteration()
    {
        $reader = new XMLReaderStub('<root><b>has</b></root>');

        /** @var ElementIterator|Node[] $it */
        $it = new ElementIterator($reader);

        $this->assertEquals(false, $it->valid());
        $this->assertSame(null, $it->valid());

        $it->rewind();
        $this->assertEquals(true, $it->valid());
        $this->assertEquals('root', $it->current()->getName());
        $this->assertEquals(0, $it->key());

        $it->rewind();
        $this->assertEquals(true, $it->valid());
        $current = $it->current();
        $this->assertEquals('root', $current->getName());
        $this->assertEquals(0, $it->key());

        $string = $current->readString();
        $this->assertEquals('has', $string);

        $it->next();
        $this->assertEquals(true, $it->valid());
        $current = $it->current();
        $this->assertEquals('b', $current->getName());
        $this->assertEquals(1, $it->key());

        $it->next();
        $this->assertEquals(false, $it->valid());
        $current = $it->current();
        $this->assertEquals(null, $current);

    }

    /** @test */
    public function getChildren()
    {
        $reader = $this->createReader();

        $it = new ElementIterator($reader);

        $xml = $it->current();
        $this->assertSame('xml', $xml->name); // ensure this is the root node
        $it->next();

        $array = $it->toArray();
        $this->assertSame(7, count($array));
        $this->assertSame("\n                test\n            ", $array['node4']);
    }

    /**
     * @test
     */
    function iterateOverNamedElements()
    {
        $reader = new XMLReaderStub('<r><a>1</a><a>2</a><b>c</b><a>3</a></r>');
        $it     = new ElementIterator($reader, 'a');

        $this->assertEquals(null, $it->valid());
        $it->rewind();
        $this->assertEquals(true, $it->valid());
        $this->assertEquals('a', $it->current()->getName());
        $it->next();
        $this->assertEquals('a', $it->current()->getName());
        $it->next();
        $this->assertEquals('a', $it->current()->getName());
        $this->assertEquals('3', $it);
        $it->next();
        $this->assertEquals(false, $it->valid());
    }

    private function createReader()
    {
        return new XMLReaderStub('<!-- -->
        <xml>
            <node1>
                <info1/>
            </node1>
            <node2 id="0">
                <info2>
                    <pool2/>
                </info2>
            </node2>
            <node3/>
            <node4>
                test
            </node4>
        </xml>');
    }
}
