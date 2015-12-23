<?php

namespace Araneum\Bundle\MainBundle\Tests\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class TopMenuTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Controller
 */
class TopMenuTest extends BaseController
{

    /**
     * Test for Edit profile link
     *
     * @runInSeparateProcess
     */
    public function testProfileLink()
    {
        $client = $this->createAdminAuthorizedClient();
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            'GET',
            $router->generate('sonata_admin_dashboard', ['_locale' => 'en'])
        );

        $link = $crawler->selectLink('Profile')->link();

        $this->assertEquals($router->match($this->getUrl($link))['_route'], 'araneum_user_user_profileShow');
    }

    /**
     * Test for Logout link
     *
     * @runInSeparateProcess
     */
    public function testLogoutLink()
    {
        $client = $this->createAdminAuthorizedClient();
        $router = $client->getContainer()->get('router');
        $crawler = $client->request(
            'GET',
            $router->generate('sonata_admin_dashboard', ['_locale' => 'en'])
        );

        $link = $crawler->filter('.glyphicon-log-out')->parents()->link();

        $crawler = $client->click($link);
        $session = $client->getContainer()->get('session');

        $this->assertTrue(count($session) == 0);
    }

    /**
     * Get route from Url
     *
     * @param Link $link
     * @return mixed
     */
    public function getUrl(Link $link)
    {
        return parse_url($link->getUri())['path'];
    }
}
