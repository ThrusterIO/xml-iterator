<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\Iteration;
use Thruster\Component\XMLIterator\XMLReader;

/**
 * Class IterationTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class IterationTest extends \PHPUnit_Framework_TestCase
{
    public function testCreation()
    {
        $iterator = new Iteration(new XMLReaderStub('<root/>'));
        $this->assertInstanceOf('\Thruster\Component\XMLIterator\Iteration', $iterator);
        $this->assertInstanceOf('\Traversable', $iterator);
        $this->assertInstanceOf('\Iterator', $iterator);
    }

    public function testIteration()
    {
        $reader   = new XMLReaderStub('<root><element></element></root>');
        $iterator = new Iteration($reader);

        $data = array(
            array(XMLReader::ELEMENT, 0),
            array(XMLReader::ELEMENT, 1),
            array(XMLReader::END_ELEMENT, 1),
            array(XMLReader::END_ELEMENT, 0),
        );

        $count = 0;

        /* @var $reader XMLReader */
        foreach ($iterator as $index => $reader) {
            $this->assertSame($count, $index);
            list($nodeType, $depth) = $data[$index];
            $this->assertSame($nodeType, $reader->nodeType);
            $this->assertSame($depth, $reader->depth);
            $count++;
        }

        $this->assertSame(4, $count);
    }

    public function testSkipNextRead()
    {
        $reader   = new XMLReaderStub('<r/>');
        $iterator = new Iteration($reader);

        $key = null;

        foreach ($iterator as $key => $node) {
            $this->assertEquals('r', $node->name);
            if ($key >= 6) {
                break;
            }
            $iterator->skipNextRead();
        }

        $this->assertEquals(6, $key);

        $reader   = new XMLReaderStub('<r><a/><a><b><c/></b></a><a></a><a/></r>');
        $iterator = new Iteration($reader);

        foreach ($iterator as $node) {
            if ($node->name === 'r') {
                continue;
            }
            $this->assertEquals(XMLReader::ELEMENT, $node->nodeType);
            $this->assertEquals('a', $node->name);

            $node->next();
            $iterator->skipNextRead();
        }
    }
}
