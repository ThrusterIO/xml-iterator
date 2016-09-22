<?php

namespace Thruster\Component\XMLIterator;

use InvalidArgumentException;

/**
 * Class AttributePreg
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class AttributePreg extends AttributeFilterBase
{
    /**
     * @var string
     */
    private $pattern;

    /**
     * @var bool
     */
    private $invert;

    /**
     * @param ElementIterator $elements
     * @param string          $attr    name of the attribute, '*' for every attribute
     * @param string          $pattern pcre based regex pattern for the attribute value
     * @param bool            $invert
     *
     * @throws InvalidArgumentException
     */
    public function __construct(ElementIterator $elements, string $attr, string $pattern, bool $invert = false)
    {
        parent::__construct($elements, $attr);

        if (false === preg_match("$pattern", '')) {
            throw new InvalidArgumentException("Invalid pcre pattern '$pattern'.");
        }

        $this->pattern = $pattern;
        $this->invert  = $invert;
    }

    public function accept()
    {
        return (bool) preg_grep($this->pattern, $this->getAttributeValues(), $this->invert ? PREG_GREP_INVERT : 0);
    }
}
