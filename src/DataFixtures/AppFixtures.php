<?php

namespace App\DataFixtures;

use App\Entity\Feed;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $feed = new Feed();
        $feed->setName("Challenges")
            ->setLink("https://www.challenges.fr/rss.xml")
            ->setActive(true);
        $manager->persist($feed);

        $feed = new Feed();
        $feed->setName("L'Express")
            ->setLink("https://www.lexpress.fr/rss/alaune.xml")
            ->setActive(true);
        $manager->persist($feed);

        $feed = new Feed();
        $feed->setName("Le Figaro")
            ->setLink("https://www.lefigaro.fr/rss/figaro_actualites.xml")
            ->setActive(true);
        $manager->persist($feed);

        $feed = new Feed();
        $feed->setName("Le Monde")
            ->setLink("https://www.lemonde.fr/rss/une.xml")
            ->setActive(true);
        $manager->persist($feed);

        $feed = new Feed();
        $feed->setName("Marianne")
            ->setLink("https://www.marianne.net/rss.xml")
            ->setActive(true);
        $manager->persist($feed);

        $manager->flush();
    }
}
