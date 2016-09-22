<?php

namespace Thruster\Component\XMLIterator;

use RuntimeException;

/**
 * Class Element
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Element extends Node
{
    private $name_;
    private $attributes_;

    public function __construct(XMLReader $reader)
    {
        parent::__construct($reader);

        $this->initializeFrom($reader);
    }

    public function getXMLElementAround($innerXML = '')
    {
        return XMLBuild::wrapTag($this->name_, $this->attributes_, $innerXML);
    }

    public function getAttributes()
    {
        return $this->attributes_;
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->attributes_[$name] ?? $default;
    }

    public function __toString()
    {
        return $this->name_;
    }

    private function initializeFrom(XMLReader $reader)
    {
        if ($reader->nodeType !== XMLReader::ELEMENT) {
            $node = new Node($reader);

            throw new RuntimeException(sprintf(
                'Reader must be at an XMLReader::ELEMENT, is XMLReader::%s given.',
                $node->getNodeTypeName()
            ));
        }

        $this->name_       = $reader->name;
        $this->attributes_ = parent::getAttributes()->getArrayCopy();
    }
}
