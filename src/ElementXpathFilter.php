<?php

namespace Thruster\Component\XMLIterator;

/**
 * Class ElementXpathFilter
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class ElementXpathFilter extends FilterBase
{
    /**
     * @var string
     */
    private $expression;

    /**
     * {@inheritDoc}
     */
    public function __construct(ElementIterator $iterator, string $expression)
    {
        parent::__construct($iterator);

        $this->expression = $expression;
    }

    public function accept()
    {
        $buffer = $this->getInnerIterator()->getNodeTree();
        $result = simplexml_load_string($buffer)->xpath($this->expression);
        $count  = count($result);

        if ($count !== 1) {
            return false;
        }

        return !($result[0]->children()->count());
    }
}
