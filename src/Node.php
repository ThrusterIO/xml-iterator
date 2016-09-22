<?php

namespace Thruster\Component\XMLIterator;

use DOMNode;
use DOMDocument;
use SimpleXMLElement;
use BadMethodCallException;

/**
 * Class Node
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
class Node
{
    /**
     * @var XMLReader
     */
    protected $reader;

    /**
     * @var int
     */
    private $nodeType;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $localName;

    /**
     * @var SimpleXMLElement
     */
    private $simpleXML;

    /**
     * @var AttributeIterator
     */
    private $attributes;

    /**
     * @var string
     */
    private $string;

    public function __construct(XMLReader $reader)
    {
        $this->reader   = $reader;
        $this->nodeType = $reader->nodeType;
        $this->name     = $reader->name;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        if (null === $this->string) {
            $this->string = $this->readString();
        }

        return $this->string;
    }

    /**
     * @return SimpleXMLElement
     */
    public function getSimpleXMLElement()
    {
        if (null === $this->simpleXML) {
            if ($this->reader->nodeType !== XMLReader::ELEMENT) {
                return null;
            }

            $node            = $this->expand();
            $this->simpleXML = simplexml_import_dom($node);
        }

        return $this->simpleXML;
    }

    /**
     * @return AttributeIterator|array
     */
    public function getAttributes()
    {
        if (null === $this->attributes) {
            $this->attributes = new AttributeIterator($this->reader);
        }

        return $this->attributes;
    }

    /**
     * @param string $name
     * @param null   $default
     *
     * @return string
     */
    public function getAttribute(string $name, $default = null)
    {
        return $this->reader->getAttribute($name) ?? $default;
    }

    /**
     * @param null $name
     * @param bool $descendantAxis
     *
     * @return ChildElementIterator
     */
    public function getChildElements($name = null, bool $descendantAxis = false) : ChildElementIterator
    {
        return new ChildElementIterator($this->reader, $name, $descendantAxis);
    }

    /**
     * @return ChildIterator|Node[]
     */
    public function getChildren() : ChildIterator
    {
        return new ChildIterator($this->reader);
    }

    /**
     * @return string name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string local name
     */
    public function getLocalName()
    {
        return $this->localName;
    }

    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @return string
     */
    public function readOuterXml()
    {
        return $this->reader->readOuterXml();
    }

    /**
     * XMLReader expand node and import it into a DOMNode with a DOMDocument
     *
     * This is for example useful for DOMDocument::saveXML() @see readOuterXml
     * or getting a SimpleXMLElement out of it @see getSimpleXMLElement
     *
     * @throws BadMethodCallException
     *
     * @param DOMNode $baseNode
     *
     * @return DOMNode
     */
    public function expand(DOMNode $baseNode = null)
    {
        if (null === $baseNode) {
            $baseNode = new DOMDocument();
        }

        if ($baseNode instanceof DOMDocument) {
            $doc = $baseNode;
        } else {
            $doc = $baseNode->ownerDocument;
        }

        if (false === $node = $this->reader->expand($baseNode)) {
            throw new BadMethodCallException('Unable to expand node.');
        }

        if ($node->ownerDocument !== $doc) {
            $node = $doc->importNode($node, true);
        }

        return $node;
    }

    /**
     * Decorated method
     *
     * @throws BadMethodCallException
     * @return string
     */
    public function readString()
    {
        return $this->reader->readString();
    }

    /**
     * Return node-type as human readable string (constant name)
     *
     * @param null $nodeType
     *
     * @return string
     */
    public function getNodeTypeName($nodeType = null)
    {
        $strings = [
            XMLReader::NONE                   => 'NONE',
            XMLReader::ELEMENT                => 'ELEMENT',
            XMLReader::ATTRIBUTE              => 'ATTRIBUTE',
            XMLREADER::TEXT                   => 'TEXT',
            XMLREADER::CDATA                  => 'CDATA',
            XMLReader::ENTITY_REF             => 'ENTITY_REF',
            XMLReader::ENTITY                 => 'ENTITY',
            XMLReader::PI                     => 'PI',
            XMLReader::COMMENT                => 'COMMENT',
            XMLReader::DOC                    => 'DOC',
            XMLReader::DOC_TYPE               => 'DOC_TYPE',
            XMLReader::DOC_FRAGMENT           => 'DOC_FRAGMENT',
            XMLReader::NOTATION               => 'NOTATION',
            XMLReader::WHITESPACE             => 'WHITESPACE',
            XMLReader::SIGNIFICANT_WHITESPACE => 'SIGNIFICANT_WHITESPACE',
            XMLReader::END_ELEMENT            => 'END_ELEMENT',
            XMLReader::END_ENTITY             => 'END_ENTITY',
            XMLReader::XML_DECLARATION        => 'XML_DECLARATION',
        ];

        if (null === $nodeType) {
            $nodeType = $this->nodeType;
        }

        return $strings[$nodeType];
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
        return call_user_func_array([$this->reader, $name], $args);
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
        return $this->reader->$name;
    }

    /**
     * debug utility method
     *
     * @param XMLReader $reader
     * @param bool      $return (optional) prints by default but can return string
     *
     * @return string|null
     */
    public static function dump(XMLReader $reader, bool $return = false)
    {
        $node     = new self($reader);
        $nodeType = $reader->nodeType;
        $nodeName = $node->getNodeTypeName();
        $extra    = '';

        if ($reader->nodeType === XMLReader::ELEMENT) {
            $extra = '<' . $reader->name . '> ';
            $extra .= sprintf("(isEmptyElement: %s) ", $reader->isEmptyElement ? 'Yes' : 'No');
        }

        if ($reader->nodeType === XMLReader::END_ELEMENT) {
            $extra = '</' . $reader->name . '> ';
        }

        if ($reader->nodeType === XMLReader::ATTRIBUTE) {
            $str = $reader->value;
            $len = strlen($str);
            if ($len > 20) {
                $str = substr($str, 0, 17) . '...';
            }
            $str   = strtr($str, ["\n" => '\n']);
            $extra = sprintf('%s = (%d) "%s" ', $reader->name, strlen($str), $str);
        }

        if ($reader->nodeType === XMLReader::TEXT || $reader->nodeType === XMLReader::WHITESPACE ||
            $reader->nodeType === XMLReader::SIGNIFICANT_WHITESPACE
        ) {
            $str = $reader->readString();
            $len = strlen($str);
            if ($len > 20) {
                $str = substr($str, 0, 17) . '...';
            }
            $str   = strtr($str, ["\n" => '\n']);
            $extra = sprintf('(%d) "%s" ', strlen($str), $str);
        }

        $label = sprintf("(#%d) %s %s", $nodeType, $nodeName, $extra);

        if ($return) {
            return $label;
        }

        printf("%s%s\n", str_repeat('  ', $reader->depth), $label);

        return null;
    }
}
