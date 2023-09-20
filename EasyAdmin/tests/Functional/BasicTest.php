<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Monolog\Logger;
class BasicTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        // echo $crawler->html();
        $this->assertResponseIsSuccessful();
        // $this->assertSelectorTextContains('div', 'Welcome to the Dashboard');
    }
}
