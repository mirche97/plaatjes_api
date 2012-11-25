<?php
namespace Api\ModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\SerializerBundle\Annotation as SER;

/**
 * Description of Album
 * @ORM\Entity()
 * @ORM\Table ()
 */
class AlbumMeta
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @SER\Groups("Id")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(name="title", type="string")
     * @SER\Groups("AlbumMeta")
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column(name="number_of_cards", type="integer")
     * @SER\Groups("AlbumMeta")
     * @var integer
     */
    protected $numberOfCards;

    /**
     * @ORM\Column(name="published_by", type="string", nullable=true)
     *
     * @var string
     */
    protected $publishedBy;

     /**
     * @ORM\Column(name="year", type="integer", nullable=true)
     * @SER\Groups("AlbumMeta")
     * @var integer
     */
    protected $year;

    /**
     * @ORM\OneToMany(targetEntity="Album", mappedBy="albumMeta")
     * @SER\Groups("AlbumMeta")
     * @var type
     */
    protected $albums;



    public function __construct()
    {
        $this->albums = new ArrayCollection();
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
     * @param string $title
     * @return AlbumMeta
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set numberOfCards
     *
     * @param integer $numberOfCards
     * @return AlbumMeta
     */
    public function setNumberOfCards($numberOfCards)
    {
        $this->numberOfCards = $numberOfCards;

        return $this;
    }

    /**
     * Get numberOfCards
     *
     * @return integer
     */
    public function getNumberOfCards()
    {
        return $this->numberOfCards;
    }

    /**
     * Set publishedBy
     *
     * @param string $publishedBy
     * @return AlbumMeta
     */
    public function setPublishedBy($publishedBy)
    {
        $this->publishedBy = $publishedBy;

        return $this;
    }

    /**
     * Get publishedBy
     *
     * @return string
     */
    public function getPublishedBy()
    {
        return $this->publishedBy;
    }

    /**
     * Set year
     *
     * @param integer $year
     * @return AlbumMeta
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }
}