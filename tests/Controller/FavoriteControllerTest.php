<?php

namespace App\Test\Controller;

use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FavoriteControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private FavoriteRepository $repository;
    private string $path = '/favorite/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Favorite::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('My favorites');

    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Favorite();
        $fixture->setTitle('Here is my title');
        $fixture->setDescription('Here is my description');
        $fixture->setDate(new \DateTime('2022-11-11 00:00:00'));
        $fixture->setGuid('lorem-ipsum');
        $fixture->setLink('https://www.google.com/');
        $fixture->setFeedName('Feed News');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/favorite/');
    }
}
