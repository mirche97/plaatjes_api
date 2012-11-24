<?php
namespace Api\ModelBundle\Controller;

use Api\CommonBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\ModelBundle\Entity\Person;

/**
 * Description of PersonController
 *
 */
class PersonController extends CommonController
{
   /**
    * get persons
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */ 
   public function getPersonsAction(Request $request)
   {
       $queryArray = $request->query->all();        
       unset($queryArray['_format']);
       
       $persons = $this->em->getRepository('ApiModelBundle:Person')->findBy($queryArray);     
       $ser = $this->container->get('serializer');
       
       return new Response($ser->serialize($persons, $this->format));
   }
   
   /**
    * get one person
    * @param integer $personId
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function getPersonAction($personId)
   {
       $person = $this->getPerson($personId);
       $serializer = $this->container->get('serializer'); 
       
       return new Response($serializer->serialize($person, $this->format));
   }
   
   /**
    * create an person
    * 
    * @param \Symfony\Component\HttpFoundation\Request $request
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function postPersonsAction(Request $request)
   {
       $content = $request->getContent();
       $person = $this->mapData($content, "Model", "Person");
       
       $this->processData($person);
       
       $location = $this->generateUrl("get_person", array("personId"=>$person->getId()));        
       $headers = array("Location" => $location);        
       $serializer = $this->container->get('serializer');       
      
       return new Response($serializer->serialize($person, $this->format), 201, $headers);                              
   }
   
   /**
    * update an person
    * 
    * @param integer                                   $personId
    * @param \Symfony\Component\HttpFoundation\Request $request
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function putPersonsAction($personId, Request $request)
   {
       $content = $request->getContent();
       $person = $this->mapData($content, "Model", "Person");
       $person->setId($personId);
       $this->processData($person);
       
       $location = $this->generateUrl("get_person", array("personId"=>$person->getId()));        
       $headers = array("Location" => $location);        
       $serializer = $this->container->get('serializer');       
       
       return new Response($serializer->serialize($person, $this->format), 201, $headers);                              
   }
   
   /**
    * delete an person
    * 
    * @param type $personId
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function deletePersonsAction($personId)
   {
        $person = $this->getPerson($personId);
        $this->em->remove($person);        
        $this->em->flush();        
        
        return new Response("", 204, array());
   }
   
   /**
    * get a person entity
    * 
    * @param integer $personId
    * 
    * @return Person
    */
   protected function getPerson($personId)
   {
       $person = $this->em->getRepository('ApiModelBundle:Person')->findOneById($personId);
       
       return $person;
   } 
   
}

