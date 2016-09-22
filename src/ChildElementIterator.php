<?php

namespace Thruster\Component\XMLIterator;

use UnexpectedValueException;

/**
 * Class ChildElementIterator
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ChildElementIterator extends ElementIterator
{
    /**
     * @var int
     */
    private $stopDepth;

    /**
     * @var bool
     */
    private $descendTree;

    /**
     * @var bool
     */
    private $didRewind;

    /**
     * @var int
     */
    private $index;

    /**
     * @inheritdoc
     *
     * @param bool $descendantAxis traverse children of children
     */
    public function __construct(XMLReader $reader, $name = null, bool $descendantAxis = false)
    {
        parent::__construct($reader, $name);

        $this->descendTree = $descendantAxis;
    }

    /**
     * @throws UnexpectedValueException
     * @return void
     */
    public function rewind()
    {
        // this iterator can not really rewind. instead it places itself onto the
        // first children.
        if ($this->reader->nodeType === XMLReader::NONE) {
            $this->moveToNextElement();
        }

        if ($this->stopDepth === null) {
            $this->stopDepth = $this->reader->depth;
        }

        // move to first child - if any
        parent::next();
        parent::rewind();

        $this->index     = 0;
        $this->didRewind = true;
    }

    public function next()
    {
        if ($this->valid()) {
            $this->index++;
        }

        while ($this->valid()) {
            parent::next();

            if ($this->descendTree || $this->reader->depth === $this->stopDepth + 1) {
                break;
            }
        };
    }

    public function valid()
    {
        if (!($valid = parent::valid())) {
            return $valid;
        }

        return $this->reader->depth > $this->stopDepth;
    }

    /**
     * @return Node
     */
    public function current()
    {
        $this->didRewind || self::rewind();

        return parent::current();
    }

    public function key()
    {
        return $this->index;
    }
}
