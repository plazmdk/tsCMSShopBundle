<?php

namespace tsCMS\ShopBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ShopControllerTest extends WebTestCase
{
    public function testBasket()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/basket');
    }

    public function testCheckout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/checkout');
    }

}
