<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\NextIteration;
use Thruster\Component\XMLIterator\Node;
use Thruster\Component\XMLIterator\XMLIterator;

/**
 * Class XMLReaderTest
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class XMLReaderTest extends XMLReaderTestCase
{
    /**
     * @dataProvider provideAllFiles
     *
     * @param string $xml
     */
    public function testReadBehavior($xml)
    {
        $reader = new XMLReaderStub($xml);

        $it       = new XMLIterator($reader);
        $expected = array();
        while ($reader->read()) {
            $expected[] = Node::dump($reader, true);
        }

        $reader->rewind();
        $index = 0;
        foreach ($it as $index => $node) {
            $this->assertEquals($expected[$index], Node::dump($reader, true));
        }

        $this->assertCount($index + 1, $expected);
    }

    /**
     * @dataProvider provideAllFiles
     *
     * @param string $xml
     */
    public function testNextBehavior($xml)
    {
        $reader = new XMLReaderStub($xml);

        $it       = new NextIteration($reader);
        $expected = array();
        while ($reader->next()) {
            $expected[] = Node::dump($reader, true);
        }

        $reader->rewind();
        $index = 0;
        foreach ($it as $index => $node) {
            $this->assertEquals($expected[$index], Node::dump($reader, true));
        }

        $this->assertCount($index + 1, $expected);
    }

    /**
     * @see readBahvior
     * @see writeBehavior
     */
    public function provideAllFiles()
    {
        $result = array();

        $path   = __DIR__ . '/Fixtures';
        $result = $this->addXmlFiles($result, $path);

        $path   = __DIR__ . '/Fixtures/data';
        $result = $this->addXmlFiles($result, $path);

        return $result;
    }
}
