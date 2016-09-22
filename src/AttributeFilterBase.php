<?php

namespace Thruster\Component\XMLIterator;

/**
 * Class AttributeFilterBase
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class AttributeFilterBase extends FilterBase
{
    private $attr;

    /**
     * @param ElementIterator $elements
     * @param string $attr name of the attribute, '*' for every attribute
     */
    public function __construct(ElementIterator $elements, $attr)
    {
        parent::__construct($elements);

        $this->attr = $attr;
    }

    protected function getAttributeValues()
    {
        $node = parent::current();

        if ('*' === $this->attr) {
            $attributes = $node->getAttributes()->getArrayCopy();
        } else {
            $attributes = (array) $node->getAttribute($this->attr);
        }

        return $attributes;
    }
}
