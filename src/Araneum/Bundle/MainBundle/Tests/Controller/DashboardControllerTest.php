<?php

namespace Araneum\Bundle\MainBundle\Tests\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        /** @var Client client */
        $this->client = self::createClient();

        /** @var router router */
        $this->router = $this->client
            ->getContainer()
            ->get('router');
    }

    /**
     * Test getting DataSource of Dashboard
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

        /** @var Response $response */
        $response = $this->client->getResponse();

        $this->assertTrue($response->isSuccessful(), $response->getContent());
        $this->assertObjectsStructuresEquals(
            (object)[
                'statistics' => (object)[
                    'applicationsState' => (object)[
                        'online' => rand(),
                        'hasProblems' => rand(),
                        'hasErrors' => rand(),
                        'disabled' => rand()
                    ],
                    'daylyApplications' => [
                        'applications' => [],
                        'errors'    => [],
                        'problems'  => [],
                        'success'   => [],
                        'disabled' => [],
                    ],
                    'daylyAverageStatuses'=>[
                        'errors' => [],
                        'problems'=>[],
                        'success' => [],
                        'disabled'  => []
                    ],
                    'clusterLoadAverage' => [],
                    'clusterUpTime' => [],
                    'summary' => [
						'applications' => [],
						'clusters' => [],
						'admins' => [],
						'connections' => [],
						'locales' => []
					],
                    'registeredCustomers' => [],
                    'receivedEmails' => []
                ]
            ],
            json_decode($response->getContent())
        );
    }
}