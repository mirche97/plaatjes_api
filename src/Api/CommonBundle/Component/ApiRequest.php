<?php

namespace Api\CommonBundle\Component;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
//use Api\CommonBundle\Component\Array2Xml;

/**
 * ApiRequest
 *
 * An extend to the default symfony request object.
 * Provides easy request access functionality
 */
class ApiRequest extends Request
{
    /**
     * @var ArrayMapper
     */
    protected $arrayMapper;

    /**
     * Sets the specified array mapper
     *
     * @param ArrayMapper $arrayMapper
     */
    public function setArrayMapper($arrayMapper=null)
    {
        if ($arrayMapper) {
            $this->arrayMapper = $arrayMapper;
        } else {
            $this->arrayMapper = new ArrayMapper( 'default');
        }    
    }

    /**
     * Maps the deserialized request data to the specified object and returns that object
     *
     * @param  object $object
     * @param  object $entityManager
     * @return object
     */
    public function getMappedObject($object, $entityManager = null)
    {
        $data = $this->getDeserializedContent();
   
        if (null === $data) {
            throw new HttpException(400, 'No request data available or invalid');
        }

        $arrayMapper = $this->arrayMapper;
        $arrayMapper->setEntityManager($entityManager);
 var_dump($arrayMapper); die();       
        $arrayMapper->map($data, $object);

        return $object;
    }

    /**
     * Deserializes XML or JSON request data and returns a generic array
     *
     * @return array|null
     */
    public function getDeserializedContent()
    {
        $data = null;
        $content = $this->getContent();

        // Check if content is JSON
        $data = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            Xml2Array::lowerCamelCaseKeys($data);
           
        } else {
            $data = null;
        }

        // Check if content is XML
        if (null === $data) {
            try {
                /**
                 * Check if the content is XML, ignore PHP errors
                 * because an Exception will be thrown for invalid input
                 */
                $xml = @new \SimpleXMLElement($content);

                // Valid XML, now count the elements
                if ($xml->count() > 0) {
                    // Parse the existing elements
                    $data = Xml2Array::createArray($content, false);
                } else {
                    // Valid XML input but no elements, so the data will be an empty array
                    $data = array();
                }
            } catch (\Exception $e) {
                // Invalid or non XML data
            }
        }

        return $data;
    }
    
    public function setRequest(Request $request)
    {
        $this->request = $request ;
    }
}