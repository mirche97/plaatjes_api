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
}

?>
