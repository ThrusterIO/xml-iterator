<?php

namespace Thruster\Component\XMLIterator;

/**
 * Class ChildIterator
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ChildIterator extends XMLIterator
{
    /**
     * @var int
     */
    private $stopDepth;

    public function __construct(XMLReader $reader)
    {
        parent::__construct($reader);

        $this->stopDepth = $reader->depth;
    }

    public function rewind()
    {
        parent::next();
        parent::rewind();
    }

    public function valid()
    {
        $parent = parent::valid();

        return $parent && ($this->reader->depth > $this->stopDepth);
    }
}
