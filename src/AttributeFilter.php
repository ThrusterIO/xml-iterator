<?php

namespace Thruster\Component\XMLIterator;

/**
 * Class AttributeFilter
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class AttributeFilter extends AttributeFilterBase
{
    private $compare;

    /**
     * @var bool
     */
    private $invert;

    /**
     * @param ElementIterator $elements
     * @param string $attr name of the attribute, '*' for every attribute
     * @param string|array $compare value(s) to compare against
     * @param bool $invert
     */
    public function __construct(ElementIterator $elements, $attr, $compare, bool $invert = false)
    {
        parent::__construct($elements, $attr);

        $this->compare = (array) $compare;
        $this->invert  = $invert;
    }

    public function accept()
    {
        $result = $this->search($this->getAttributeValues(), $this->compare);

        return $this->invert ? !$result : $result;
    }

    private function search($values, $compares)
    {
        foreach ($compares as $compare) {
            if (in_array($compare, $values)) {
                return true;
            }
        }

        return false;
    }
}

