<?php

namespace Api\CommonBundle\Component;

/**
 * Xml2Array: A class to convert XML to array in PHP
 * It returns the array which can be converted back to XML using the Array2XML script
 * It takes an XML string or a DOMDocument object as an input.
 *
 * See Array2XML: http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
 *
 * Author : Lalit Patel
 * Website: http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array
 * License: Apache License 2.0
 *          http://www.apache.org/licenses/LICENSE-2.0
 * Version: 0.1 (07 Dec 2011)
 * Version: 0.2 (04 Mar 2012)
 * 			Fixed typo 'DomDocument' to 'DOMDocument'
 * Usage:
 *       $array = Xml2Array::createArray($xml);
 * 
 * Pieter Vogelaar: Made psr-2 compliant
 *                  Added includeRootTag option to get the same array as a JSON decode
 *                  Added indexedArrayKey option to support indexed arrays from XML input
 *                  Added lowerCamelCaseKeys option to let all keys start with a lowercase character
 *                  Added convertStringBooleans option to get the same array as a JSON decode
 */
class Xml2Array
{
    private static $xml = null;
    private static $encoding = 'UTF-8';
    private static $indexedArrayKey = 'entry'; // Multiple nodes after eachother with this tag name will result
                                               // in an indexed array
    private static $lowerCamelCaseKeys = true; // All keys will start with a lowercase character
    private static $convertStringBooleans = true; // Element with string value "true" or "false" (case insensitive)
                                                  // will become a real PHP boolean data type
    
    /**
     * Initialize the root XML node [optional]
     * 
     * @param string  $version
     * @param string  $encoding
     * @param string  $indexedArrayKey
     * @param boolean $lowerCamelCaseKeys
     * @param boolean $convertStringBooleans
     */
    public static function init($version = '1.0', $encoding = 'UTF-8', $indexedArrayKey = 'entry',
        $lowerCamelCaseKeys = true, $convertStringBooleans = true
    ) {
        self::$xml = new \DOMDocument($version, $encoding);
        self::$encoding = $encoding;
        self::$indexedArrayKey = $indexedArrayKey;
        self::$lowerCamelCaseKeys = $lowerCamelCaseKeys;
        self::$convertStringBooleans = $convertStringBooleans;
    }

    /**
     * Convert an XML to Array
     * 
     * @param  string  $inputXml
     * @param  boolean $includeRootTag
     * @return array
     */
    public static function createArray($inputXml, $includeRootTag = false)
    {
        $xml = self::getXMLRoot();
        if (is_string($inputXml)) {
            $parsed = $xml->loadXML($inputXml);
            if (!$parsed) {
                throw new \Exception('[Xml2Array] Error parsing the XML string.');
            }
        } else {
            if (get_class($inputXml) != 'DOMDocument') {
                throw new \Exception('[Xml2Array] The input XML object should be of type: DOMDocument.');
            }
            $xml = self::$xml = $inputXml;
        }
        
        if (true === $includeRootTag) {
            $array[$xml->documentElement->tagName] = self::convert($xml->documentElement);
        } else {
            $array = self::convert($xml->documentElement);
        }
        
        if (true === self::$lowerCamelCaseKeys) {
            self::lowerCamelCaseKeys($array);
        }
        
        self::$xml = null; // clear the xml node in the class for 2nd time use.
        return $array;
    }

    /**
     * All keys in the specified array will start with a lowercase character
     * 
     * @param array $array
     */
    public static function lowerCamelCaseKeys(array &$array)
    {
        foreach ($array as $key => &$value) {
            // Convert key
            $newKey = lcfirst($key);

            // Change key if needed
            if ($newKey != $key) {
                unset($array[$key]);
                $array[$newKey] = $value;
            }

            // Handle nested arrays
            if (is_array($value)) {
                self::lowerCamelCaseKeys($value);
            }
        }
    }
    
    /**
     * Convert XML to an Array
     * 
     * @param  mixed $node XML as a string or as an object of DOMDocument
     * @return mixed
     */
    private static function &convert($node)
    {
        $output = array();

        switch ($node->nodeType) {
            case XML_COMMENT_NODE:
                // Ignore comments
                $output = '';
                break;
            
            case XML_CDATA_SECTION_NODE:
                $output['@cdata'] = trim($node->textContent);
                break;

            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                
                // convert string booleans if wanted
                if (true === self::$convertStringBooleans && is_string($output)
                    && in_array(strtolower($output), array('true', 'false'))
                ) {
                    $output = 'false' == strtolower($output) ? false : true;
                }
                
                break;

            case XML_ELEMENT_NODE:
                // for each child node, call the covert function recursively
                for ($i = 0, $m = $node->childNodes->length; $i < $m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = self::convert($child);
                    if (isset($child->tagName)) {
                        $t = $child->tagName;

                        // assume more nodes of same kind are coming
                        if (!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    } else {
                        //check if it is not an empty text node
                        if ($v !== '') {
                            $output = $v;
                        }
                    }
                }

                if (is_array($output)) {
                    // if only one node of its kind, assign it directly instead if array($value);
                    foreach ($output as $t => $v) {
                        if (is_array($v) && count($v) == 1 && $t != self::$indexedArrayKey) {
                            $output[$t] = $v[0];
                        }
                    }
                    if (empty($output)) {
                        //for empty nodes
                        $output = '';
                    }
                }

                // loop through the attributes and collect them
                if ($node->attributes->length) {
                    $a = array();
                    foreach ($node->attributes as $attrName => $attrNode) {
                        $a[$attrName] = (string) $attrNode->value;
                    }
                    // if its an leaf node, store the value in @value instead of directly storing it.
                    if (!is_array($output)) {
                        $output = array('@value' => $output);
                    }
                    $output['@attributes'] = $a;
                }
                break;
        }
        
        /**
         * Check if the array must be converted to an indexed array.
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
         * will become (unless an @attributes key exists):
         *
         * Array
         * (
         *     [0] => Example item 1
         *     [1] => Example item 2
         * )
         */
        if (is_array($output) && array_key_exists(self::$indexedArrayKey, $output)) {
            // If an @attributes key exist, prevent level up to avoid the loss of attributes
            if (!array_key_exists('@attributes', $output)) {
                $output = $output[self::$indexedArrayKey];
            }
        }
        
        return $output;
    }
    
    /**
     * Get the root XML node, if there isn't one, create it.
     */
    private static function getXMLRoot()
    {
        if (empty(self::$xml)) {
            self::init();
        }
        
        return self::$xml;
    }
}