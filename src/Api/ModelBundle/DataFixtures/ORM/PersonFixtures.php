<?php
namespace Api\ModelBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\ModelBundle\Entity\Person;

/**
 * Person Data Fixtures
 *
 */
class PersonFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager) 
    {
        $person1 = new Person();
        $person1->setFirstName("Mirjana");
        $person1->setLastName("de Jonge");
        $person1->setEmail("mirche97@hotmail.com");
        $person1->setNickname("mirche");
        $manager->persist($person1);
        
        $person2 = new Person();
        $person2->setFirstName("Danny");
        $person2->setLastName("de Jonge");
        $person2->setEmail("danny70@live.nl");
        $person2->setNickname("timespender");
        $manager->persist($person2);
        
        $manager->flush();
        
        $this->setReference("mira",$person1);
        $this->setReference("danny", $person2);
                       
    }
    
    public function getOrder()
    {
        return 10;
    }
}

?>
