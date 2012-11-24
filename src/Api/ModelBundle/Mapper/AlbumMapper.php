<?php
namespace Api\ModelBundle\Mapper;

use Api\CommonBundle\Component\AbstractRequestMapper;
use Api\ModelBundle\Entity\Album;

class AlbumMapper extends AbstractRequestMapper
{
    
    /**
     * 
     * @param string  $data
     * @param integer $albumId
     * 
     * @return Album
     * 
     * @throws \InvalidArgumentException
     */
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

         $album = $this->mapProperty($album, 'title', $request);
         $album = $this->mapProperty($album, 'owner', $request);
         
         return $album;
     }
     
     
}