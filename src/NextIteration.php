<?php

namespace Thruster\Component\XMLIterator;

use Iterator;

/**
 * Class NextIteration
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class NextIteration implements Iterator
{
    /**
     * @var XMLReader
     */
    private $reader;

    /**
     * @var int
     */
    private $index;

    /**
     * @var bool
     */
    private $valid;

    /**
     * @var string
     */
    private $localName;

    public function __construct(XMLReader $reader, $localName = null)
    {
        $this->reader    = $reader;
        $this->localName = $localName;
    }

    public function rewind()
    {
        $this->moveReaderToCurrent();
        $this->index = 0;
    }

    public function valid()
    {
        return $this->valid;
    }

    public function current()
    {
        return $this->reader;
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        $this->valid && $this->index++;

        if ($this->localName) {
            $this->valid = $this->reader->next($this->localName);
        } else {
            $this->valid = $this->reader->next();
        }
    }

    /**
     * move cursor to the next element but only if it's not yet there
     */
    private function moveReaderToCurrent()
    {
        if (($this->reader->nodeType === XMLReader::NONE) ||
            ($this->reader->nodeType !== XMLReader::ELEMENT) ||
            ($this->localName && $this->localName !== $this->reader->localName)
        ) {
            self::next();
        }
    }
}
