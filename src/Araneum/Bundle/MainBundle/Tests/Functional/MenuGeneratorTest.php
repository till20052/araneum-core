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

/**
 * Class MenuGeneratorTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional
 */
class MenuGeneratorTest extends WebTestCase
{

    /**
     *  Test left menu
     */
    public function testLeftMenu()
    {

        $client = static::createClient();

        $client->request(
            'GET',
            '/manage/menu.json'
        );

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
