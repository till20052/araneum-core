<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Controller\AdminApplicationController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

/**
 * Class AdminApplicationControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class AdminApplicationControllerTest extends BaseController
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
     * Test Enable Action
     * @runInSeparateProcess
     */
    public function testEnableAction()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->router->generate('araneum_main_admin_application_enable'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
        /**
         * @var Response $response
         */
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }

    /**
     * Test Disable Action
     * @runInSeparateProcess
     */
    public function testDisabledAction()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->router->generate('araneum_main_admin_application_disable'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
        /**
         * @var Response $response
         */
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful());
    }

    /**
     * Test Check Status Action
     */
    public function testCheckStatusAction()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->router->generate('araneum_applications_admin_application_status'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );
        /**
         * @var Response $response
         */
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful());
    }
}
