<?php

namespace Thruster\Component\XMLIterator\Tests;

use Thruster\Component\XMLIterator\XMLReader;

/**
 * Class XMLReaderStub
 *
 * @package Thruster\Component\XMLIterator\Tests
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class XMLReaderStub extends XMLReader
{
    private $xml;

    public function __construct($xml)
    {
        $this->xml = $xml;
        $this->rewind();
    }

    public function rewind()
    {
        $xml = $this->xml;

        if ($xml[0] === '<') {
            $xml = 'data://text/xml;base64,' . base64_encode($this->xml);
        }

        $this->open($xml);
    }
}
