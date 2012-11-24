<?php
namespace Api\ModelBundle\Controller;

use Api\CommonBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\ModelBundle\Entity\AlbumMeta;

/**
 * Description of AlbumMetaController
 *
 */
class AlbumMetaController extends CommonController
{
   /**
    * get albumMetas metadata
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */ 
   public function getAlbummetasAction(Request $request)
   {
       $queryArray = $request->query->all();        
       unset($queryArray['_format']);
       
       $albumMetas = $this->em->getRepository('ApiModelBundle:AlbumMeta')->findBy($queryArray);     
       $ser = $this->container->get('serializer');
       
       return new Response($ser->serialize($albumMetas, $this->format));
   }
   
   /**
    * get one albumMeta
    * @param integer $albumMetaId
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function getAlbummetaAction($albumMetaId)
   {
       $albumMeta = $this->getAlbumMeta($albumMetaId);
       $serializer = $this->container->get('serializer'); 
       
       return new Response($serializer->serialize($albumMeta, $this->format));
   }
   
   /**
    * create an albumMeta
    * 
    * @param \Symfony\Component\HttpFoundation\Request $request
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function postAlbummetasAction(Request $request)
   {
       $content = $request->getContent();
       $albumMeta = $this->mapData($content, "Model", "AlbumMeta");
       
       $this->processData($albumMeta);
       
       $location = $this->generateUrl("get_albummeta", array("albumMetaId"=>$albumMeta->getId()));        
       $headers = array("Location" => $location);        
       $serializer = $this->container->get('serializer');       
      
       return new Response($serializer->serialize($albumMeta, $this->format), 201, $headers);                              
   }
   
   /**
    * update an albumMeta
    * 
    * @param integer                                   $albumMetaId
    * @param \Symfony\Component\HttpFoundation\Request $request
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function putAlbummetasAction($albumMetaId, Request $request)
   {
       $content = $request->getContent();
       $albumMeta = $this->mapData($content, "Model", "AlbumMeta");
       $albumMeta->setId($albumMetaId);
       $this->processData($albumMeta);
       
       $location = $this->generateUrl("get_albummeta", array("albumMetaId"=>$albumMeta->getId()));        
       $headers = array("Location" => $location);        
       $serializer = $this->container->get('serializer');       
       
       return new Response($serializer->serialize($albumMeta, $this->format), 201, $headers);                              
   }
   
   /**
    * delete an albumMeta
    * 
    * @param type $albumMetaId
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function deleteAlbummetasAction($albumMetaId)
   {
        $albumMeta = $this->getAlbumMeta($albumMetaId);
        $this->em->remove($albumMeta);        
        $this->em->flush();        
        
        return new Response("", 204, array());
   }
   
   /**
    * get an albumMeta entity
    * 
    * @param integer $albumMetaId
    * 
    * @return AlbumMeta
    */
   protected function getAlbumMeta($albumMetaId)
   {
       $albumMeta = $this->em->getRepository('ApiModelBundle:AlbumMeta')->findOneById($albumMetaId);
       
       return $albumMeta;
   } 
   
}

