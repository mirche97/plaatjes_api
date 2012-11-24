<?php
namespace Api\ModelBundle\Mapper;

use Api\CommonBundle\Component\AbstractRequestMapper;
use Api\ModelBundle\Entity\Person;

class PersonMapper extends AbstractRequestMapper
{
    
    /**
     * 
     * @param string  $data
     * @param integer $personId
     * 
     * @return Person
     * 
     * @throws \InvalidArgumentException
     */
     public function map($data, $personId=null)
     {
         $request = json_decode($data);  
         
         if (json_last_error()) {            
             throw new \InvalidArgumentException("Json is invalid");       
         }
         
         if (empty($personId)) {
             $person = new Person();
         } else {
             $person = $this->doctrine->getEntityManager()->find('ApiModelBundle:Person',$personId);
         }

         $person = $this->mapProperty($person, 'firstName', $request);
         $person = $this->mapProperty($person, 'lastName', $request);
         $person = $this->mapProperty($person, 'email', $request);
         $person = $this->mapProperty($person, 'nickName', $request);
         
         return $person;
     }
     
     
}