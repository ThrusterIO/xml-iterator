<?php

namespace Thruster\Component\XMLIterator;

use FilterIterator;

/**
 * Class FilterBase
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class FilterBase extends FilterIterator
{
    public function __construct(XMLIterator $elements)
    {
        parent::__construct($elements);
    }

    /**
     * @return XMLReader
     */
    public function getReader()
    {
        return $this->getInnerIterator()->getReader();
    }
}
