<?php
namespace Api\ModelBundle\Controller;

use Api\CommonBundle\Controller\CommonController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\ModelBundle\Entity\Card;

/**
 * Description of CardController
 *
 */
class CardController extends CommonController
{
   /**
    * get cards
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function getCardsAction(Request $request)
   {
       $queryArray = $request->query->all();
       unset($queryArray['_format']);

       $cards = $this->em->getRepository('ApiModelBundle:Card')->findBy($queryArray);
       $ser = $this->container->get('serializer');
       $ser->setGroups(array("Card", "Id"));

       return new Response($ser->serialize($cards, $this->format));
   }

   /**
    * get one card
    * @param integer $cardId
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function getCardAction($cardId)
   {
       $card = $this->getCard($cardId);
       $ser = $this->container->get('serializer');
       $ser->setGroups(array("Card", "Id"));

       return new Response($ser->serialize($card, $this->format));
   }

   /**
    * create an card
    *
    * @param \Symfony\Component\HttpFoundation\Request $request
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function postCardsAction(Request $request)
   {
       $content = $request->getContent();
       $card = $this->mapData($content, "Model", "Card");

       $this->processData($card);

       $location = $this->generateUrl("get_card", array("cardId"=>$card->getId()));
       $headers = array("Location" => $location);
       $ser = $this->container->get('serializer');
       $ser->setGroups(array("Card", "Id"));

       return new Response($ser->serialize($card, $this->format), 201, $headers);
   }

   /**
    * update an card
    *
    * @param integer                                   $cardId
    * @param \Symfony\Component\HttpFoundation\Request $request
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function putCardsAction($cardId, Request $request)
   {
       $content = $request->getContent();
       $card = $this->mapData($content, "Model", "Card");
       $card->setId($cardId);
       $this->processData($card);

       $location = $this->generateUrl("get_card", array("cardId"=>$card->getId()));
       $headers = array("Location" => $location);
       $ser = $this->container->get('serializer');
       $ser->setGroups(array("Card", "Id"));

       return new Response($ser->serialize($card, $this->format), 201, $headers);
   }

   /**
    * delete an card
    *
    * @param type $cardId
    *
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function deleteCardsAction($cardId)
   {
        $card = $this->getCard($cardId);
        $this->em->remove($card);
        $this->em->flush();

        return new Response("", 204, array());
   }

   /**
    * get a card entity
    *
    * @param integer $cardId
    *
    * @return Card
    */
   protected function getCard($cardId)
   {
       $card = $this->em->getRepository('ApiModelBundle:Card')->findOneById($cardId);

       return $card;
   }

}

