<?php

namespace Thruster\Component\XMLIterator;

use Traversable;

/**
 * Class XMLBuild
 *
 * @package Thruster\Component\XMLIterator
 * @author  Aurimas Niekis <aurimas@niekis.lt>
 */
abstract class XMLBuild
{
    /**
     * indentLines()
     *
     * this will add a line-separator at the end of the last line because if it was
     * empty it is not any longer and deserves one.
     *
     * @param string $lines
     * @param string $indent (optional)
     *
     * @return string
     */
    public static function indentLines($lines, $indent = '  ')
    {
        $lineSeparator = "\n";
        $buffer        = '';
        $line          = strtok($lines, $lineSeparator);

        while ($line) {
            $buffer .= $indent . $line . $lineSeparator;
            $line = strtok($lineSeparator);
        }

        strtok(null, null);

        return $buffer;
    }

    /**
     * @param string            $name
     * @param array|Traversable $attributes attributeName => attributeValue string pairs
     * @param bool              $emptyTag   create an empty element tag (commonly known as short tags)
     *
     * @return string
     */
    public static function startTag($name, $attributes, $emptyTag = false)
    {
        $buffer = '<' . $name;
        $buffer .= static::attributes($attributes);
        $buffer .= $emptyTag ? '/>' : '>';

        return $buffer;
    }

    /**
     * @param array|Traversable $attributes attributeName => attributeValue string pairs
     *
     * @return string
     */
    public static function attributes($attributes)
    {
        $buffer = '';
        foreach ($attributes as $name => $value) {
            $buffer .= ' ' . $name . '="' . static::attributeValue($value) . '"';
        }

        return $buffer;
    }

    /**
     * @param string $value
     *
     * @see XMLBuild::numericEntitiesSingleByte
     *
     * @return string
     */
    public static function attributeValue($value)
    {
        $buffer = $value;
        // REC-xml/#AVNormalize - preserve
        // REC-xml/#sec-line-ends - preserve
        $buffer = preg_replace_callback('~\r\n|\r(?!\n)|\t~', ['self', 'numericEntitiesSingleByte'], $buffer);

        return htmlspecialchars($buffer, ENT_QUOTES, 'UTF-8', false);
    }

    /**
     * @param string            $name
     * @param array|Traversable $attributes attributeName => attributeValue string pairs
     * @param string            $innerXML
     *
     * @return string
     */
    public static function wrapTag($name, $attributes, $innerXML)
    {
        if (!strlen($innerXML)) {
            return XMLBuild::startTag($name, $attributes, true);
        }

        return
            XMLBuild::startTag($name, $attributes)
            . "\n"
            . XMLBuild::indentLines($innerXML)
            . "</$name>";
    }

    /**
     * @param XMLReader $reader
     *
     * @return string
     */
    public static function readerNode(XMLReader $reader)
    {
        switch ($reader->nodeType) {
            case XMLREADER::NONE:
                return '%(0)%';
            case XMLReader::ELEMENT:
                return XMLBuild::startTag($reader->name, new AttributeIterator($reader));
            default:
                $node         = new Node($reader);
                $nodeTypeName = $node->getNodeTypeName();
                $nodeType     = $reader->nodeType;

                return sprintf('%%%s (%d)%%', $nodeTypeName, $nodeType);
        }
    }

    /**
     * @param array $matches
     *
     * @return string
     * @see attributeValue()
     */
    private static function numericEntitiesSingleByte($matches)
    {
        $buffer = str_split($matches[0]);

        foreach ($buffer as &$char) {
            $char = sprintf('&#%d;', ord($char));
        }

        return implode('', $buffer);
    }
}
