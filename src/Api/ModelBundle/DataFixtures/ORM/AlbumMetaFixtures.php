<?php
namespace Api\ModelBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\ModelBundle\Entity\AlbumMeta;

/**
 * AlbumMeta Data Fixtures
 *
 */
class AlbumMetaFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $am1 = new AlbumMeta();
        $am1->setTitle("Superdieren 2012");
        $am1->setImage("superdieren2012");
        $am1->setNumberOfCards(204);
        $am1->setPublishedBy("Albert Heijn");
        $manager->persist($am1);
        $manager->flush();

        $this->setReference("superdieren-2012", $am1);

    }

    public function getOrder()
    {
        return 10;
    }
}

?>
