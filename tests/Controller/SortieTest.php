<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SortieTest extends WebTestCase
{
    public function testSortieIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/sortie/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('span', 'Toutes les sorties');
    }
}
