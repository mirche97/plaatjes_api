<?php
namespace Api\ModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Api\ModelBundle\Entity\AlbumMeta;

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
     * @ORM\ManyToOne(targetEntity="AlbumMeta", inversedBy="albums")
     * @ORM\JoinColumn(name="albummeta_id", referencedColumnName="id")
     * 
     * @var AlbumMeta 
     */
    protected $albumMeta;
    
    /**
     * @ORM\OneToMany(targetEntity="Card", mappedBy="album")
     * 
     * @var array<\api\ModelBundle\Entity\Card> 
     */
    protected $cards;
    
    /**
     * @ORM\ManyToOne(targetEntity="Person", inversedBy="albums")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     * 
     * @var Person 
     */
    protected $owner;
    
    /**
     * name, if a person has more of the same albums (same albumMeta)
     * 
     * @ORM\Column(name="name", type="string", nullable=true)
     * @var string 
     */
    protected $name;

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
     * Set name
     *
     * @param string $name
     * @return Album
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
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
    
    /**
     * set AlbumMeta
     * 
     * @param \Api\ModelBundle\Entity\AlbumMeta $albumMeta
     * 
     * @return \Api\ModelBundle\Entity\Album
     */
    public function setAlbumMeta(AlbumMeta $albumMeta)
    {
        $this->albumMeta = $albumMeta;
        
        return $this;
    }
    
    /**
     * get albumMeta
     * 
     * @return AlbumMeta
     */
    public function getAlbumMeta()
    {
        return $this->albumMeta;
    }
}