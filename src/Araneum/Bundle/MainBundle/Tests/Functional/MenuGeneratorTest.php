<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 06.11.15
 * Time: 9:34
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class MenuGeneratorTest extends WebTestCase
{

    /**
     *  Test left menu
     */
    public function testLeftMenu()
    {

        $client = static::createClient();

        $crawler = $client->request(
            'GET',
            '/en/manage/menu.json'
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}