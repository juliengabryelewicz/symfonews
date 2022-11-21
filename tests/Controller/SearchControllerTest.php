<?php

namespace App\Test\Controller;

use App\Entity\Search;
use App\Repository\SearchRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SearchControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private SearchRepository $repository;
    private string $path = '/searchlist/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Search::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('My search list');

    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'search[name]' => 'Tout sur l\'astrologie',
            'search[search_query]' => 'astrologie'
        ]);

        self::assertResponseRedirects('/searchlist/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Search();
        $fixture->setName('Tout sur l\'astrologie');
        $fixture->setSearchQuery('astrologie');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'search[name]' => 'Tout savoir sur l\'astrologie',
            'search[search_query]' => 'astrologie science'
        ]);

        self::assertResponseRedirects('/searchlist/');

        $fixture = $this->repository->findAll();

        self::assertSame('Tout savoir sur l\'astrologie', $fixture[0]->getName());
        self::assertSame('astrologie science', $fixture[0]->getLink());
        self::assertSame(false, $fixture[0]->getActive());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Search();
        $fixture->setName('Tout sur l\'astrologie');
        $fixture->setSearchQuery('astrologie');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/searchlist/');
    }
}
