<?php
namespace Api\ModelBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\ModelBundle\Entity\Album;

/**
 * Album Data Fixtures
 *
 */
class AlbumFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    
    public function load(ObjectManager $manager) 
    {
        $album1 = new Album();
        $album1->setOwner($manager->merge($this->getReference("mira")));
        $album1->setAlbumMeta($manager->merge($this->getReference("superdieren-2012")));
        $manager->persist($album1);
        
        $album2 = new Album();
        $album2->setOwner($manager->merge($this->getReference("danny")));
        $album2->setAlbumMeta($manager->merge($this->getReference("superdieren-2012")));
        $manager->persist($album2);
        
        
        $manager->flush();
        
        $this->setReference("albumMira", $album1);
        $this->setReference("albumDanny", $album2);
                       
    }
    
    public function getOrder()
    {
        return 20;
    }
}

?>
