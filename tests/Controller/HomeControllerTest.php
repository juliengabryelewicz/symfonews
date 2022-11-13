<?php

namespace App\Test\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private string $path = '/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Homepage');

    }


    public function testSearch(): void
    {
        $crawler = $this->client->request('GET', $this->path."search?q=recherche");

        self::assertResponseStatusCodeSame(200);

    }

}
