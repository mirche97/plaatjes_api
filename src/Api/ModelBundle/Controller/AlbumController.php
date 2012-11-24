<?php
namespace Api\ModelBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Api\CommonBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Api\CommonBundle\Component\ApiRequest;
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
   public function getAlbumsAction()
   {
       $content = $request->get('Title');
      // var_dump($content);die();
       
       $album = new Album();
       $album->setTitle("Superdieren");
       
       $ser = $this->container->get('serializer');
       $serializedAlbum = $ser->serialize($album,'json');
       return new Response($serializedAlbum);
   }
   
   public function postAlbumsAction(Request $request)
   {
      // $album = $this->getRequest()->getMappedObject(new Album(), $this->em);
       $content = $request->getContent();
       $this->mapData($content, "Model", "Album");
       
       
       die();
       
              
       return new Response($content);
   }
}

