<?php
namespace Api\ModelBundle\Mapper;

use Api\CommonBundle\Component\AbstractRequestMapper;
use Api\ModelBundle\Entity\AlbumMeta;

class AlbumMetaMapper extends AbstractRequestMapper
{
    
    /**
     * 
     * @param string  $data
     * @param integer $albumMetaId
     * 
     * @return Album<eta
     * 
     * @throws \InvalidArgumentException
     */
     public function map($data, $albumMetaId=null)
     {
         $request = json_decode($data);  
         
         if (json_last_error()) {            
             throw new \InvalidArgumentException("Json is invalid");       
         }
         
         if (empty($albumMetaId)) {
             $albumMeta = new AlbumMeta();
         } else {
             $albumMeta = $this->doctrine->getEntityManager()->find('ApiModelBundle:AlbumMeta', $albumMetaId);
         }

         $albumMeta = $this->mapProperty($albumMeta, 'title', $request);
         $albumMeta = $this->mapProperty($albumMeta, 'numbeOfCards', $request);
         $albumMeta = $this->mapProperty($albumMeta, 'year', $request);
         $albumMeta = $this->mapProperty($albumMeta, 'publishedBy', $request);
         
         return $albumMeta;
     }
     
     
}