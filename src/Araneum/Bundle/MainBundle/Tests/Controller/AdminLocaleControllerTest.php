<?php

namespace Araneum\Bundle\MainBundle\Tests\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Router;

/**
 * Class AdminLocaleControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Controller
 */
class AdminLocaleControllerTest extends BaseController
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
        $this->client = self::createAdminAuthorizedClient();

        /** @var Router router */
        $this->router = $this->client->getContainer()->get('router');
    }

    /**
     * Test Initialize Action
     */
    public function testInitAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->router->generate('araneum_manage_locales_init'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        /** @var Response $response */
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
     */
    public function testDatatableAction()
    {
        $this->client->request(
            Request::METHOD_GET,
            $this->router->generate('araneum_manage_locales_grid'),
            [],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        /** @var Response $response */
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

    /**
     * Asserting Structures of Objects are Equal
     *
     * @param \stdClass $expected
     * @param \stdClass $actual
     */
    private function assertObjectsStructuresEquals(\stdClass $expected, \stdClass $actual)
    {
        foreach ($expected as $key => $value) {
            $this->assertObjectHasAttribute($key, $actual, json_encode($actual));

            if (is_object($value)) {
                $this->assertObjectsStructuresEquals($value, $actual->{$key});
            }
        }
    }
}
