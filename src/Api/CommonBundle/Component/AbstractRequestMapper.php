<?php
namespace Api\CommonBundle\Component;

/**
 * Description of AbstractRequestMapper
 *
 * @author mirche
 */
abstract class AbstractRequestMapper implements MapperInterface
{
    protected $doctrine;    
    protected $deserializer;
    
    /**
     * {@inheritdoc}
     */
    public function setDoctrine($doctrine) 
    {
        $this->doctrine = $doctrine;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDeserializer($serializer)    
    {        
        $this->deserializer = $serializer;    
    }
    
    /**
     * map an object property
     * 
     * @param type $object
     * @param type $property
     * @param type $request
     * @return type
     */
    public function mapProperty($object, $property, $request)
    {
        $capProperty = ucfirst($property);
        $method = 'set'.$capProperty;
    
        if (isset($request->$capProperty)) {
            $object->$method($request->$capProperty);
             
        }
         
        return $object;
    }
}

?>
