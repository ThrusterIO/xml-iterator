<?php

namespace Thruster\Component\XMLIterator;

/**
 * Class NodeTypeFilter
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class NodeTypeFilter extends FilterBase
{
    /**
     * @var array
     */
    private $allowed;

    /**
     * @var XMLReader
     */
    private $reader;

    /**
     * @var bool
     */
    private $invert;

    /**
     * @param XMLIterator $iterator
     * @param int|int[] $nodeType one or more type constants  <http://php.net/class.xmlreader>
     *      XMLReader::NONE            XMLReader::ELEMENT         XMLReader::ATTRIBUTE       XMLReader::TEXT
     *      XMLReader::CDATA           XMLReader::ENTITY_REF      XMLReader::ENTITY          XMLReader::PI
     *      XMLReader::COMMENT         XMLReader::DOC             XMLReader::DOC_TYPE        XMLReader::DOC_FRAGMENT
     *      XMLReader::NOTATION        XMLReader::WHITESPACE      XMLReader::SIGNIFICANT_WHITESPACE
     *      XMLReader::END_ELEMENT     XMLReader::END_ENTITY      XMLReader::XML_DECLARATION
     * @param bool $invert
     */
    public function __construct(XMLIterator $iterator, $nodeType, bool $invert = false)
    {
        parent::__construct($iterator);

        $this->allowed = (array) $nodeType;
        $this->reader  = $iterator->getReader();
        $this->invert  = $invert;
    }

    public function accept()
    {
        $result = in_array($this->reader->nodeType, $this->allowed);

        return $this->invert ? !$result : $result;
    }
}
