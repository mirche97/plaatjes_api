<?php
namespace Api\CommonBundle\Component;

interface MapperInterface
{
    /**
     * set doctrine
     * 
     * @param  \Doctrine\Bundle\DoctrineBundle\Registry  $doctrine
     */
    public function setDoctrine($doctrine);
    
    /**
     * Set JMS deserializer object
     * 
     * @param \JMS\SerializerBundle\Serializer  $serializer
     */
    public function setDeserializer($serializer);
    
    /**
     * 
     * @param string $data
     * @param integer $objectId
     */
    public function map($data, $objectId=null); 
}

?>
