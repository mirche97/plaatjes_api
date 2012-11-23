<?php
namespace api\ModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Album
 * @ORM\Entity()
 * @ORM\Table ()
 */
class Album
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @var integer 
     */
    protected $id;
    
    /**
     * @ORM\Column(name="title")
     * 
     * @var string 
     */
    protected $title;
    
    /*r*
     * @ORM\OneToMany(targetEntity="Card", mappedBy="album")
     * 
     * @var array<\api\ModelBundle\Entity\Card> 
     */
    protected $cards;
    
    protected $owner;
    
    

    public function __construct()
    {
        $this->cards = new ArrayCollection();
    }
    
    /**     
     * Set $id     
     *      
     * @param int $id     
     */    
    public function setId($id)    
    {        
        $this->id = $id;    
        
    }
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Album
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
   
    /**
     * Get cards
     * @return array<Card>
     */
    public function getCards()
    {
        return $this->cards;
    }
    
    /**
     * set cards
     * @param \Doctrine\Common\Collections\ArrayCollection $cards
     * 
     * @return \api\ModelBundle\Entity\Album
     */
    public function setCards(ArrayCollection $cards)
    {
        $this->cards = $cards;
        
        return $this;
    }   
    
    /**
     * add a card
     * 
     * @param \api\ModelBundle\Entity\Card $card
     * 
     * @return \api\ModelBundle\Entity\Album
     */
    public function addCard(Card $card)
    {
        $this->cards[] = $card;
        
        return $this;
    }
}