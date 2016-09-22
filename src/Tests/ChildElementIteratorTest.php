<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\ChildElementIterator;
use Thruster\Component\XMLIterator\ElementIterator;

/**
 * Class ChildElementIteratorTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ChildElementIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testOteration()
    {
        $reader = new XMLReaderStub('<!-- comment --><root><child></child></root>');

        $it = new ChildElementIterator($reader);

        $this->assertEquals(false, $it->valid());
        $this->assertSame(null, $it->valid());

        $it->rewind();
        $this->assertEquals(true, $it->valid());
        $this->assertEquals('child', $it->current()->getName());

        $it->next();
        $this->assertEquals(false, $it->valid());

        $reader = new XMLReaderStub('<root><none></none><one><child></child></one><none></none></root>');
        $base   = new ElementIterator($reader);
        $base->rewind();
        $root = $base->current();
        $this->assertEquals('root', $root->getName());
        $children = $root->getChildElements();
        $this->assertEquals('root', $reader->name);
        $children->rewind();
        $this->assertEquals('none', $reader->name);
        $children->next();
        $this->assertEquals('one', $reader->name);
        $childChildren = new ChildElementIterator($reader);
        $this->assertEquals('child', $childChildren->current()->getName());
        $childChildren->next();
        $this->assertEquals(false, $childChildren->valid());
        $this->assertEquals('none', $reader->name);
        $childChildren->next();
        $this->assertEquals('none', $reader->name);

        $this->assertEquals(true, $children->valid());
        $children->next();
        $this->assertEquals(false, $children->valid());


        // children w/o descendants
        $reader->rewind();
        $expected = ['none', 'one', 'none'];
        $root     = $base->current();
        $this->assertEquals('root', $root->getName());

        $count = 0;
        foreach ($root->getChildElements() as $index => $child) {
            $this->assertSame($count++, $index);
            $this->assertEquals($expected[$index], $reader->name);
        }
        $this->assertEquals(count($expected), $count);

        // children w/ descendants
        $reader->rewind();
        $expected = ['none', 'one', 'child', 'none'];
        $root     = $base->current();
        $this->assertEquals('root', $root->getName());

        $count = 0;
        foreach ($root->getChildElements(null, true) as $index => $child) {
            $this->assertSame($count++, $index);
            $this->assertEquals($expected[$index], $reader->name);
        }
        $this->assertEquals(count($expected), $count);

    }
}
