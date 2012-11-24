<?php
namespace Api\ModelBundle\Mapper;

use Api\CommonBundle\Component\AbstractRequestMapper;
use Api\ModelBundle\Entity\Album;

class AlbumMapper extends AbstractRequestMapper
{
    
     public function map($data, $albumId=null)
     {
         $request = json_decode($data);  
         
         if (json_last_error()) {            
             throw new \InvalidArgumentException("Json is invalid");       
         }
         
         if (empty($albumId)) {
             $album = new Album();
         } else {
             $album = $this->doctrine->getEntityManager()->find('ApiModelBundle:Album',$albumId);
         }
         
         if (isset($request->{'Title'})) {
             $album->setTitle($request->{'Title'});
         }
      
         if (isset($request->{'Owner'}->{'Id'})) {
             
         }
         var_dump($album); die();
     }
}