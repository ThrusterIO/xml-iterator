<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\XMLIterator;
use Thruster\Component\XMLIterator\XMLReader;

/**
 * Class XMLIteratorTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class XMLIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testOteration()
    {
        $reader = new XMLReaderStub('<r><a>1</a><a>2</a></r>');

        $it = new XMLIterator($reader);
        $this->assertSame(null, $it->valid());

        $it->rewind();
        $this->assertSame(true, $it->valid());

        $node = $it->current();
        $this->assertEquals('r', $node->getName());
        $this->assertEquals('12', (string) $node);

        $it->moveToNextElementByName('a');
        $node = $it->current();
        $this->assertEquals('a', $node->getName());
        $this->assertEquals('1', (string) $node);

        $it->moveToNextElementByName('a');
        $node = $it->current();
        $this->assertEquals('a', $node->getName());
        $this->assertEquals('2', (string) $node);

        $it->next();
        $it->next();
        $this->assertEquals(XMLReader::END_ELEMENT, $reader->nodeType);
        $this->assertEquals('a', $it->current()->getName());

        $it->next();
        $it->next();
        $this->assertEquals(false, $it->valid());
    }
}
