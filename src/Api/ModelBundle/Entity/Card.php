<?php
namespace Api\ModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Api\ModelBundle\Enum\CardStatus;



/**
 * Description of Card
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class Card {
   
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @var integer 
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Album", inversedBy="cards")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id") 
     * 
     * @var Album
     */
    protected $album;
    
    /**
     * number
     * @ORM\Column(name="number", type="integer")
     * @var integer
     */
    protected $number;
    
    /**
     * status
     * @ORM\Column(name="status", type="integer")
     * @var integer 
     */
    protected $status;
    
    /**
     * possible statuses
     * @var array 
     */
    protected $statuses = array(
        CardStatus::IN_ALBUM =>"in album",
        CardStatus::DOUBLE =>"dubbele",
        CardStatus::RESERVED =>"gereserveerd"
    );

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
     * Set album
     *
     * @param api\ModelBundle\Entity\Album $album
     * @return Card
     */
    public function setAlbum(\api\ModelBundle\Entity\Album $album = null)
    {
        $this->album = $album;
    
        return $this;
    }

    /**
     * Get album
     *
     * @return api\ModelBundle\Entity\Album 
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return Card
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Card
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
   
    /**
     * get status as string
     * 
     * @return string
     */
    public function getStatusString(){
        
        return $this->statuses[$this->status];
    }
}