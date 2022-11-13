<?php

namespace App\Test\Controller;

use App\Entity\Feed;
use App\Repository\FeedRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FeedControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private FeedRepository $repository;
    private string $path = '/feed/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Feed::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('My Feeds');

    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'feed[name]' => 'Challenges',
            'feed[link]' => 'https://www.challenges.fr/rss.xml',
            'feed[active]' => true,
        ]);

        self::assertResponseRedirects('/feed/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Feed();
        $fixture->setName('Challenges');
        $fixture->setLink('https://www.challenges.fr/rss.xml');
        $fixture->setActive(true);

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'feed[name]' => 'New Challenges',
            'feed[link]' => 'https://www.challenges.fr/rss.xml',
            'feed[active]' => false,
        ]);

        self::assertResponseRedirects('/feed/');

        $fixture = $this->repository->findAll();

        self::assertSame('New Challenges', $fixture[0]->getName());
        self::assertSame('https://www.challenges.fr/rss.xml', $fixture[0]->getLink());
        self::assertSame(false, $fixture[0]->getActive());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Feed();
        $fixture->setName('Challenges');
        $fixture->setLink('https://www.challenges.fr/rss.xml');
        $fixture->setActive(true);

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/feed/');
    }
}
