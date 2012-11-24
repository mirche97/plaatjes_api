<?php
namespace Api\ModelBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Entity of Person
 *
 * @ORM\Entity()
 * @ORM\Table()
 */
class Person {
    
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @var integer 
     */
    protected $id;
    
    /**
     * @ORM\Column(name="firstname")
     * 
     * @var string 
     */
    protected $firstName;
    
    /**
     * @ORM\Column(name="lastname")
     * 
     * @var string 
     */
    protected $lastName;
    
    /**
     * @ORM\Column(name="email")
     * 
     * @var string 
     */
    protected $email;
    
    /**
     * @ORM\Column(name="nickname")
     * 
     * @var string 
     */
    protected $nickname;
    
    /**
     * @ORM\OneToMany(targetEntity="Album", mappedBy="owner")
     * 
     * @var type 
     */
    protected $albums;
    
    /**
     * @ORM\ManyToMany(targetEntity="Person", mappedBy="myFriends")
     */
    protected $friendsWithMe;

    /**
     * @ORM\ManyToMany(targetEntity="Person", inversedBy="friendsWithMe")
     * @ORM\JoinTable(name="friends",
     *      joinColumns={@ORM\JoinColumn(name="person_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="friend_person_id", referencedColumnName="id")}
     *      )
     */
    protected $myFriends;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
        $this->friendsWithMe = new \Doctrine\Common\Collections\ArrayCollection();
        $this->myFriends = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set firstName
     *
     * @param string $firstName
     * @return Person
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return Person
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Person
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nickname
     *
     * @param string $nickname
     * @return Person
     */
    public function setNickname($nickname)
    {
        $this->nickname = $nickname;
    
        return $this;
    }

    /**
     * Get nickname
     *
     * @return string 
     */
    public function getNickname()
    {
        return $this->nickname;
    }

    /**
     * Add albums
     *
     * @param Api\ModelBundle\Entity\Album $albums
     * @return Person
     */
    public function addAlbum(\Api\ModelBundle\Entity\Album $albums)
    {
        $this->albums[] = $albums;
    
        return $this;
    }

    /**
     * Remove albums
     *
     * @param Api\ModelBundle\Entity\Album $albums
     */
    public function removeAlbum(\Api\ModelBundle\Entity\Album $albums)
    {
        $this->albums->removeElement($albums);
    }

    /**
     * Get albums
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAlbums()
    {
        return $this->albums;
    }

    /**
     * Add friendsWithMe
     *
     * @param Api\ModelBundle\Entity\Person $friendsWithMe
     * @return Person
     */
    public function addFriendsWithMe(\Api\ModelBundle\Entity\Person $friendsWithMe)
    {
        $this->friendsWithMe[] = $friendsWithMe;
    
        return $this;
    }

    /**
     * Remove friendsWithMe
     *
     * @param Api\ModelBundle\Entity\Person $friendsWithMe
     */
    public function removeFriendsWithMe(\Api\ModelBundle\Entity\Person $friendsWithMe)
    {
        $this->friendsWithMe->removeElement($friendsWithMe);
    }

    /**
     * Get friendsWithMe
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getFriendsWithMe()
    {
        return $this->friendsWithMe;
    }

    /**
     * Add myFriends
     *
     * @param Api\ModelBundle\Entity\Person $myFriends
     * @return Person
     */
    public function addMyFriend(\Api\ModelBundle\Entity\Person $myFriends)
    {
        $this->myFriends[] = $myFriends;
    
        return $this;
    }

    /**
     * Remove myFriends
     *
     * @param Api\ModelBundle\Entity\Person $myFriends
     */
    public function removeMyFriend(\Api\ModelBundle\Entity\Person $myFriends)
    {
        $this->myFriends->removeElement($myFriends);
    }

    /**
     * Get myFriends
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getMyFriends()
    {
        return $this->myFriends;
    }
}