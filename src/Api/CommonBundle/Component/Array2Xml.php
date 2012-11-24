<?php

namespace Api\CommonBundle\Component;

/**
 * Array2Xml: A class to convert array in PHP to XML
 * 
 * It also takes into account attributes names unlike SimpleXML in PHP
 * It returns the XML in form of DOMDocument class for further manipulation.
 * It throws exception if the tag name or attribute name has illegal chars.
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 * Version: 0.1 (10 July 2011)
 * Version: 0.2 (16 August 2011)
 *          - replaced htmlentities() with htmlspecialchars() (Thanks to Liel Dulev)
 *          - fixed a edge case where root node has a false/null/0 value. (Thanks to Liel Dulev)
 * Version: 0.3 (22 August 2011)
 *          - fixed tag sanitize regex which didn't allow tagnames with single character.
 * Version: 0.4 (18 September 2011)
 *          - Added support for CDATA section using @cdata instead of @value.
 * Version: 0.5 (07 December 2011)
 *          - Changed logic to check numeric array indices not starting from 0.
 * Version: 0.6 (04 March 2012)
 *          - Code now doesn't @cdata to be placed in an empty array
 * Version: 0.7 (24 March 2012)
 *          - Reverted to version 0.5
 * Version: 0.8 (02 May 2012)
 *          - Removed htmlspecialchars() before adding to text node or attributes.
 *
 * Usage:
 *       $xml = Array2Xml::createXML('root_node_name', $php_array);
 *       echo $xml->saveXML();
 * 
 * Pieter Vogelaar: Made psr-2 compliant
 *                  Added indexedArrayKeyForSameKindNodes option for the same behaviour with JSON and indexed arrays
 */
class Array2Xml
{
    private static $xml = null;
    private static $encoding = 'UTF-8';
    private static $indexedArrayKey = 'entry';
    
    /**
     * If true, an indexed array will be converted to XML with sibling nodes that have the tag name
     * of self::$indexedArrayKey. If false, the sibling nodes will have the same name as the parent array key.
     * 
     * @var boolean
     */
    private static $indexedArrayKeyForSameKindNodes = true;

    /**
     * Initialize the root XML node [optional]
     * 
     * @param $version
     * @param $encoding
     * @param $formatOutput
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $formatOutput = true,
        $indexedArrayKeyForSameKindNodes = true, $indexedArrayKey = 'entry'
    ) {
        self::$xml = new \DOMDocument($version, $encoding);
        self::$xml->formatOutput = $formatOutput;
        self::$encoding = $encoding;
        self::$indexedArrayKeyForSameKindNodes = $indexedArrayKeyForSameKindNodes;
        self::$indexedArrayKey = $indexedArrayKey;
    }

    /**
     * Convert an Array to XML
     * 
     * @param string $node_name - name of the root node to be converted
     * @param array $arr - aray to be converterd
     * @return \DOMDocument
     */
    public static function createXml($node_name, $arr = array())
    {
        $xml = self::getXmlRoot();
        
        if (true === self::$indexedArrayKeyForSameKindNodes) {
            self::levelDownIndexedArrays($arr);
        }
        
        $xml->appendChild(self::convert($node_name, $arr));

        self::$xml = null;    // clear the xml node in the class for 2nd time use.
        return $xml;
    }

    /**
     * Levels down indexed arrays
     * 
     * Indexed array becomes child of indexedArrayKey
     * 
     * Array
     * (
     *     [0] => Example item 1
     *     [1] => Example item 2
     * )
     * 
     * will become:
     *
     * Array
     * (
     *     [entry] => Array
     *         (
     *             [0] => Example item 1
     *             [1] => Example item 2
     *         )
     * )
     * 
     * @param array $arr
     * @param array $parent
     */
    private static function levelDownIndexedArrays(&$arr, $parent = null)
    {
        if (null === $parent) {
            $parent = $arr;
        }
        
        foreach ($arr as $key => &$value) {
            if (is_array($value)) {
                if (isset($value[0]) && self::$indexedArrayKey != $key) {
                    $arr[$key] = array(self::$indexedArrayKey => $value);
                } else {
                    self::levelDownIndexedArrays($value, $parent);
                }
            }
        } 
    }
    
    /**
     * Convert an Array to XML
     * 
     * @param  string $node_name Name of the root node to be converted
     * @param  array  $arr       Aray to be converterd
     * @return DOMNode
     */
    private static function &convert($node_name, $arr = array())
    {
        $xml = self::getXmlRoot();
        $node = $xml->createElement($node_name);

        if (is_array($arr)) {
            // get the attributes first.;
            if (isset($arr['@attributes'])) {
                foreach ($arr['@attributes'] as $key => $value) {
                    if (!self::isValidTagName($key)) {
                        throw new \Exception('[Array2Xml] Illegal character in attribute name. attribute: ' . $key . ' in node: ' . $node_name);
                    }
                    $node->setAttribute($key, self::bool2str($value));
                }
                unset($arr['@attributes']); //remove the key from the array once done.
            }

            // check if it has a value stored in @value, if yes store the value and return
            // else check if its directly stored as string
            if (isset($arr['@value'])) {
                $node->appendChild($xml->createTextNode(self::bool2str($arr['@value'])));
                unset($arr['@value']);    //remove the key from the array once done.
                //return from recursion, as a note with value cannot have child nodes.
                return $node;
            } else if (isset($arr['@cdata'])) {
                $node->appendChild($xml->createCDATASection(self::bool2str($arr['@cdata'])));
                unset($arr['@cdata']);    //remove the key from the array once done.
                //return from recursion, as a note with cdata cannot have child nodes.
                return $node;
            }
        }

        // create subnodes using recursion
        if (is_array($arr)) {
            // recurse to get the node for that key
            foreach ($arr as $key => $value) {
                if (!self::isValidTagName($key)) {
                    throw new \Exception('[Array2Xml] Illegal character in tag name. tag: ' . $key . ' in node: ' . $node_name);
                }
                if (is_array($value) && is_numeric(key($value))) {
                    // MORE THAN ONE NODE OF ITS KIND;
                    // if the new array is numeric index, means it is array of nodes of the same kind
                    // it should follow the parent key name
                    foreach ($value as $k => $v) {
                        $node->appendChild(self::convert($key, $v));
                    }
                } else {
                    // ONLY ONE NODE OF ITS KIND
                    $node->appendChild(self::convert($key, $value));
                }
                unset($arr[$key]); //remove the key from the array once done.
            }
        }

        // after we are done with all the keys in the array (if it is one)
        // we check if it has any text value, if yes, append it.
        if (!is_array($arr)) {
            $node->appendChild($xml->createTextNode(self::bool2str($arr)));
        }

        return $node;
    }
    
    /**
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXmlRoot()
    {
        if (empty(self::$xml)) {
            self::init();
        }
        return self::$xml;
    }
    
    /**
     * Get string representation of boolean value
     */
    private static function bool2str($v)
    {
        //convert boolean to text value.
        $v = $v === true ? 'true' : $v;
        $v = $v === false ? 'false' : $v;
        return $v;
    }
    
    /**
     * Check if the tag name or attribute name contains illegal characters
     * Ref: http://www.w3.org/TR/xml/#sec-common-syn
     */
    private static function isValidTagName($tag)
    {
        $pattern = '/^[a-z_]+[a-z0-9\:\-\.\_]*[^:]*$/i';
        return \preg_match($pattern, $tag, $matches) && $matches[0] == $tag;
    }
}