<?php

namespace Thruster\Component\XMLIterator;

use Iterator;
use BadMethodCallException;

/**
 * Class Iteration
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Iteration implements Iterator
{
    /**
     * @var XMLReader
     */
    private $reader;

    /**
     * @var bool
     */
    private $valid;

    /**
     * @var int
     */
    private $index;

    /**
     * @var bool
     */
    private $skipNextRead;

    public function __construct(XMLReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * skip the next read on next next()
     *
     * this is useful of the reader has moved to the next node already inside a foreach iteration and the next
     * next would move the reader one off.
     *
     * @see next
     */
    public function skipNextRead()
    {
        $this->skipNextRead = true;
    }

    /**
     * @return XMLReader
     */
    public function current()
    {
        return $this->reader;
    }

    public function next()
    {
        $this->index++;

        if ($this->skipNextRead) {
            $this->skipNextRead = false;
            $this->valid        = $this->reader->nodeType;
        } else {
            $this->valid = $this->reader->read();
        }
    }

    public function key()
    {
        return $this->index;
    }

    public function valid()
    {
        return $this->valid;
    }

    public function rewind()
    {
        if ($this->reader->nodeType !== XMLReader::NONE) {
            throw new BadMethodCallException('Reader can not be rewound');
        }

        $this->index = 0;
        $this->valid = $this->reader->read();
    }
}
