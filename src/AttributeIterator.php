<?php

namespace Thruster\Component\XMLIterator;

use Iterator as IteratorInterface;
use Countable;
use ArrayAccess;
use BadMethodCallException;

/**
 * Class AttributeIterator
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class AttributeIterator implements IteratorInterface, Countable, ArrayAccess
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
     * @var array
     */
    private $array;

    public function __construct(XMLReader $reader)
    {
        $this->reader = $reader;
    }

    public function count()
    {
        return $this->reader->attributeCount;
    }

    public function current()
    {
        return $this->reader->value;
    }

    public function key()
    {
        return $this->reader->name;
    }

    public function next()
    {
        $this->valid = $this->reader->moveToNextAttribute();

        if (!$this->valid) {
            $this->reader->moveToElement();
        }
    }

    public function rewind()
    {
        $this->valid = $this->reader->moveToFirstAttribute();
    }

    public function valid()
    {
        return $this->valid;
    }

    public function getArrayCopy()
    {
        if ($this->array === null) {
            $this->array = iterator_to_array($this);
        }

        return $this->array;
    }

    public function getAttributeNames()
    {
        return array_keys($this->getArrayCopy());
    }

    public function offsetExists($offset)
    {
        $attributes = $this->getArrayCopy();

        return isset($attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        $attributes = $this->getArrayCopy();

        return $attributes[$offset];
    }

    public function offsetSet($offset, $value)
    {
        throw new BadMethodCallException('XMLReader attributes are read-only');
    }

    public function offsetUnset($offset)
    {
        throw new BadMethodCallException('XMLReader attributes are read-only');
    }

    /**
     * @return XMLReader
     */
    public function getReader()
    {
        return $this->getReader();
    }
}
