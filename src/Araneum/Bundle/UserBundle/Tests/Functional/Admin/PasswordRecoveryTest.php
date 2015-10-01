<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\DomCrawler\Link;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class PasswordRecoveryTest extends BaseController
{
    /**
     * @var EntityManager
     */
    private static $manager;

    /**
     * @var EntityRepository
     */
    private static $repository;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::bootKernel();

        self::$manager = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        self::$repository = self::$manager->getRepository('AraneumUserBundle:User');
    }

    /**
     * Test for Password recovery link
     *
     * @runInSeparateProcess
     */
    public function testRecovery()
    {
        $client = $this->createAdminAuthorizedClient();
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            'GET',
            $router->generate('sonata_admin_dashboard', ['_locale' => 'en'])
        );

        $crawler = $client->request(
            'GET',
            $router->generate('araneum_user_user_profileShow', ['_locale' => 'en'])
        );

        $link = $crawler->selectLink('Forgot password?')->link();

        $this->assertEquals($router->match($this->getUrl($link))['_route'], 'fos_user_resetting_request');
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