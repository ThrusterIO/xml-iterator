<?php

namespace Thruster\Component\XMLIterator;

/**
 * Class ElementIterator
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ElementIterator extends XMLIterator
{
    /**
     * @var int
     */
    private $index;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $didRewind;

    /**
     * @param XMLReader   $reader
     * @param null|string $name element name, leave empty or use '*' for all elements
     */
    public function __construct(XMLReader $reader, $name = null)
    {
        parent::__construct($reader);

        $this->setName($name);
    }

    /**
     * @return void
     */
    public function rewind()
    {
        parent::rewind();

        $this->ensureCurrentElementState();
        $this->didRewind = true;
        $this->index     = 0;
    }

    /**
     * @return Node|null
     */
    public function current()
    {
        $this->didRewind || self::rewind();
        $this->ensureCurrentElementState();

        return self::valid() ? new Node($this->reader) : null;
    }

    public function key()
    {
        return $this->index;
    }

    public function next()
    {
        if (parent::valid()) {
            $this->index++;
        }
        parent::next();
        $this->ensureCurrentElementState();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $array = [];
        $this->didRewind || $this->rewind();
        if (!$this->valid()) {
            return [];
        }
        $this->ensureCurrentElementState();
        while ($this->valid()) {
            $element = new Node($this->reader);
            if ($this->reader->hasValue) {
                $string = $this->reader->value;
            } else {
                $string = $element->readString();
            }
            if ($this->name) {
                $array[] = $string;
            } else {
                $array[$element->name] = $string;
            }
            $this->moveToNextElementByName($this->name);
        }

        return $array;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->readString();
    }

    /**
     * decorate method calls
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($name, $args)
    {
        return call_user_func_array([$this->current(), $name], $args);
    }

    /**
     * decorate property get
     *
     * @param string $name
     *
     * @return string
     */
    public function __get($name)
    {
        return $this->current()->$name;
    }

    /**
     * @param null|string $name
     */
    public function setName($name = null)
    {
        $this->name = '*' === $name ? null : $name;
    }

    /**
     * take care the underlying XMLReader is at an element with a fitting name (if $this is looking for a name)
     */
    private function ensureCurrentElementState()
    {
        if ($this->reader->nodeType !== XMLReader::ELEMENT) {
            $this->moveToNextElementByName($this->name);
        } elseif ($this->name && $this->name !== $this->reader->name) {
            $this->moveToNextElementByName($this->name);
        }
    }
}
