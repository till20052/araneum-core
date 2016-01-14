<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Admin
 */
class DashboardControllerTest extends BaseController
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
     * Test getting DataSource of Dashboard
     * @runInSeparateProcess
     */
    public function testGetDataSourceAction()
    {
        $this->client
            ->request(
                Request::METHOD_GET,
                $this->router->generate('araneum_admin_dashboard_getDataSource', ['_locale' => 'en']),
                [],
                [],
                ['HTTP_X-Requested-With' => 'XMLHttpRequest']
            );

        /**
         * @var Response $response
         */
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful(), $response->getContent());
        $this->assertObjectsStructuresEquals(
            (object) [
                'statistics' => (object) [
                    'applicationsState' => (object) [
                        'online' => rand(),
                        'hasProblems' => rand(),
                        'hasErrors' => rand(),
                        'disabled' => rand(),
                    ],
                    'daylyApplications' => [
                        'applications' => [],
                        'errors' => [],
                        'problems' => [],
                        'success' => [],
                        'disabled' => [],
                    ],
                    'daylyAverageStatuses' => [
                        'errors' => [],
                        'problems' => [],
                        'success' => [],
                        'disabled' => [],
                    ],
                    'clusterLoadAverage' => [],
                    'clusterUpTime' => [],
                    'summary' => [
                        'applications' => [],
                        'clusters' => [],
                        'admins' => [],
                        'connections' => [],
                        'locales' => [],
                    ],
                    'registeredCustomers' => [],
                    'receivedEmails' => [],
                ],
                'charts' => [
                    'leads' => [
                        'count' => rand(),
                        'data' => [],
                    ],
                ],
            ],
            json_decode($response->getContent())
        );
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        /**
         * @var Client client
         */
        $this->client = self::createAdminAuthorizedClient('admin');

        /**
         * @var router router
         */
        $this->router = $this->client
            ->getContainer()
            ->get('router');
    }
}
