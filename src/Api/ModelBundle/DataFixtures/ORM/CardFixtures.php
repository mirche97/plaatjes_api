<?php
namespace Api\ModelBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Api\ModelBundle\Entity\Card;

/**
 * Card Data Fixtures
 *
 */
class CardFixtures extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        for ($i=1; $i<=10; $i++) {
             $card = new Card();
             $card->setAlbum($manager->merge($this->getReference("albumMira")));
             $card->setNumber($i);
             $card->setStatus(0); //in album
             $manager->persist($card);
             $this->setReference("albumMira-".$i, $card);

             if ($i>=8) {
                 $card = new Card();
                 $card->setAlbum($manager->merge($this->getReference("albumMira")));
                 $card->setNumber($i);
                 $card->setStatus(1); //double
                 $manager->persist($card);
                 $this->setReference("albumMira-".$i."-double", $card);

                 if ($i==10) {
                     $card = new Card();
                     $card->setAlbum($manager->merge($this->getReference("albumMira")));
                     $card->setNumber($i);
                     $card->setStatus(2); //reserved
                     $manager->persist($card);
                     $this->setReference("albumMira-".$i."-reserved", $card);
                 }
             }
         }

         for ($i=11; $i<=20; $i++) {
             $card = new Card();
             $card->setAlbum($manager->merge($this->getReference("albumDanny")));
             $card->setNumber($i);
             $card->setStatus(0); //in album
             $manager->persist($card);
             $this->setReference("albumDanny-".$i, $card);

             if ($i>=18) {
                 $card = new Card();
                 $card->setAlbum($manager->merge($this->getReference("albumDanny")));
                 $card->setNumber($i);
                 $card->setStatus(1); //double
                 $manager->persist($card);
                 $this->setReference("albumDanny-".$i."-double", $card);

                 if ($i==20) {
                     $card = new Card();
                     $card->setAlbum($manager->merge($this->getReference("albumDanny")));
                     $card->setNumber($i);
                     $card->setStatus(2); //reserved
                     $manager->persist($card);
                     $this->setReference("albumDanny-".$i."-reserved", $card);
                 }
             }
         }

        $manager->flush();

    }

    public function getOrder()
    {
        return 30;
    }
}

?>
