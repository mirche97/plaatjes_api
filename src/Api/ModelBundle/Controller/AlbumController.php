<?php
namespace Api\ModelBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Api\CommonBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
//use Api\CommonBundle\Component\ApiRequest;
use Api\CommonBundle\Component\ArrayMapper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception;
use Api\ModelBundle\Entity\Album;

/**
 * Description of AlbumController
 *
 */
class AlbumController extends CommonController
{
   /**
    * get albums
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */ 
   public function getAlbumsAction(Request $request)
   {
       $queryArray = $request->query->all();        
       unset($queryArray['_format']);
       
       $albums = $this->em->getRepository('ApiModelBundle:Album')->findBy($queryArray);     
       $ser = $this->container->get('serializer');
       
       return new Response($ser->serialize($albums, $this->format));
   }
   
   /**
    * get one album
    * @param integer $albumId
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function getAlbumAction($albumId)
   {
       $album = $this->getAlbum($albumId);
       $serializer = $this->container->get('serializer'); 
       
       return new Response($serializer->serialize($album, $this->format));
   }
   
   /**
    * create an album
    * 
    * @param \Symfony\Component\HttpFoundation\Request $request
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function postAlbumsAction(Request $request)
   {
       $content = $request->getContent();
       $album = $this->mapData($content, "Model", "Album");
       
       $this->processData($album);
       
       $location = $this->generateUrl("get_album", array("albumId"=>$album->getId()));        
       $headers = array("Location" => $location);        
       $serializer = $this->container->get('serializer');       
      
       return new Response($serializer->serialize($album, $this->format), 201, $headers);                              
   }
   
   /**
    * update an album
    * 
    * @param integer                                   $albumId
    * @param \Symfony\Component\HttpFoundation\Request $request
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function putAlbumsAction($albumId, Request $request)
   {
       $content = $request->getContent();
       $album = $this->mapData($content, "Model", "Album");
       $album->setId($albumId);
       $this->processData($album);
       
       $location = $this->generateUrl("get_album", array("albumId"=>$album->getId()));        
       $headers = array("Location" => $location);        
       $serializer = $this->container->get('serializer');       
       
       return new Response($serializer->serialize($album, $this->format), 201, $headers);                              
   }
   
   /**
    * delete an album
    * 
    * @param type $albumId
    * 
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function deleteAlbumsAction($albumId)
   {
        $album = $this->getAlbum($albumId);
        $this->em->remove($album);        
        $this->em->flush();        
        
        return new Response("", 204, array());
   }
   
   protected function getAlbum($albumId)
   {
       $album = $this->em->getRepository('ApiModelBundle:Album')->findOneById($albumId);
       
       return $album;
   } 
   
}

