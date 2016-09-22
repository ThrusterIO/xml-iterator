<?php

namespace Thruster\Component\XMLIterator;

use Iterator as IteratorInterface;

/**
 * Class XMLIterator
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class XMLIterator implements IteratorInterface
{
    /**
     * @var XMLReader
     */
    protected $reader;

    /**
     * @var int
     */
    private $index;

    /**
     * @var bool
     */
    private $lastRead;

    /**
     * @var array
     */
    private $elementStack;

    public function __construct(XMLReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @return XMLReader
     */
    public function getReader() : XMLReader
    {
        return $this->reader;
    }

    /**
     * @param string $name
     *
     * @return bool|Node
     */
    public function moveToNextElementByName($name = null)
    {
        while (self::moveToNextElement()) {
            if (!$name || $name === $this->reader->name) {
                break;
            }

            self::next();
        };

        return self::valid() ? self::current() : false;
    }

    public function moveToNextElement()
    {
        return $this->moveToNextByNodeType(XMLReader::ELEMENT);
    }

    /**
     * @param int $nodeType
     *
     * @return bool|Node
     */
    public function moveToNextByNodeType($nodeType)
    {
        if (null === self::valid()) {
            self::rewind();
        } elseif (self::valid()) {
            self::next();
        }

        while (self::valid()) {
            if ($this->reader->nodeType === $nodeType) {
                break;
            }

            self::next();
        }

        return self::valid() ? self::current() : false;
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        // this iterator can not really rewind
        if ($this->reader->nodeType === XMLREADER::NONE) {
            self::next();
        } elseif ($this->lastRead === null) {
            $this->lastRead = true;
        }

        $this->index = 0;
    }

    /**
     * @inheritdoc
     */
    public function valid()
    {
        return $this->lastRead;
    }

    /**
     * @return Node
     */
    public function current()
    {
        return new Node($this->reader);
    }

    /**
     * @inheritdoc
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * @inheritdoc
     */
    public function next()
    {
        if ($this->lastRead = $this->reader->read() and $this->reader->nodeType === XMLReader::ELEMENT) {
            $depth                      = $this->reader->depth;
            $this->elementStack[$depth] = new Element($this->reader);

            if (count($this->elementStack) !== $depth + 1) {
                $this->elementStack = array_slice($this->elementStack, 0, $depth + 1);
            }
        }

        $this->index++;
    }

    /**
     * @return string
     */
    public function getNodePath() : string
    {
        return '/' . implode('/', $this->elementStack);
    }

    /**
     * @return string
     */
    public function getNodeTree() : string
    {
        $stack  = $this->elementStack;
        $buffer = '';

        /* @var $element Element */
        while ($element = array_pop($stack)) {
            $buffer = $element->getXMLElementAround($buffer);
        }

        return $buffer;
    }
}
