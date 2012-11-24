<?php
namespace Api\ModelBundle\Mapper;

use Api\CommonBundle\Component\AbstractRequestMapper;
use Api\ModelBundle\Entity\Card;

class CardMapper extends AbstractRequestMapper
{
    
    /**
     * 
     * @param string  $data
     * @param integer $cardId
     * 
     * @return Card
     * 
     * @throws \InvalidArgumentException
     */
     public function map($data, $cardId=null)
     {
         $request = json_decode($data);  
         
         if (json_last_error()) {            
             throw new \InvalidArgumentException("Json is invalid");       
         }
         
         if (empty($cardId)) {
             $card = new Card();
         } else {
             $card = $this->doctrine->getEntityManager()->find('ApiModelBundle:Card',$cardId);
         }

         $card = $this->mapProperty($card, 'number', $request);
         $card = $this->mapProperty($card, 'status', $request);
         $card = $this->mapProperty($card, 'album', $request);
         
         return $card;
     }
     
     
}