<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;
use Araneum\Bundle\UserBundle\Controller\AdminUserController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * Class AdminUserControllerTest
 *
 * @package Araneum\Bundle\UserBundle\Tests\Functional\Controller
 */
class AdminUserControllerTest extends BaseController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->client = self::createAdminAuthorizedClient('admin');

        /**
         * @var Router router
         */
        $this->router = $this->client->getContainer()->get('router');
    }

    /**
     * Test Initialize Action
     * @runInSeparateProcess
     */
    public function testInitAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->router->generate('araneum_manage_users_init'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        /**
         * @var Response $response
         */
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertObjectsStructuresEquals(
            (object) [
                'grid' => [
                    'columns' => [],
                ],
            ],
            json_decode($response->getContent())
        );
    }

    /**
     * Test datatable
     * @runInSeparateProcess
     */
    public function testDatatableAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->router->generate('araneum_manage_users_grid'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        /**
         * @var Response $response
         */
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertObjectsStructuresEquals(
            (object) [
                'aaData' => [],
                'iTotalDisplayRecords' => rand(),
                'iTotalRecords' => rand(),
                'sEcho' => rand(),
            ],
            json_decode($response->getContent())
        );
    }
}
