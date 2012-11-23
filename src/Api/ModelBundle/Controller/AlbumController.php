<?php
namespace Api\ModelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception;

/**
 * Description of AlbumController
 *
 */
class AlbumController extends Controller
{
   public function getAlbumsAction(Request $request)
   {
       $content = $request->get('Title');
      // var_dump($content);die();
       return new Response("ensponset");
   }
   
   public function postAlbumsAction(Request $request)
   {
       $content = $request->getContent();
      // var_dump($content);die();
       return new Response($content);
   }
}

