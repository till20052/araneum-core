<?php

namespace Araneum\Bundle\AgentBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SpotApiAdapterControllerTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Functional\Controller
 */
class SpotApiAdapterControllerTest extends BaseController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Data for test find action
     *
     * @return array
     */
    public function actionDataProvider()
    {
        return [
            'Normal' => [
                [
                    'appKey' => ApplicationFixtures::TEST_APP_APP_KEY,
                    'MODULE' => 'Country',
                    'COMMAND' => 'view',
                ],
                Response::HTTP_OK,
                1,
            ],
            'NormalWithRequest' => [
                [
                    'appKey' => ApplicationFixtures::TEST_APP_APP_KEY,
                    'MODULE' => 'Country',
                    'COMMAND' => 'view',
                    'requestData' => 'requestData',
                ],
                Response::HTTP_OK,
                1,
            ],
            'BadData' => [
                [
                    'appKey' => ApplicationFixtures::TEST_APP_APP_KEY,
                    'MODULE' => 'Country',
                ],
                Response::HTTP_NOT_FOUND,
                1,
            ],
        ];
    }

    /**
     * Test requestAction in SpotAdapterApiController
     *
     * @dataProvider actionDataProvider
     * @runInSeparateProcess
     *
     * @param array $filters
     * @param int   $expectedStatusCode
     * @param int   $expectedFindResultsCount
     */
    public function testFindAction($filters, $expectedStatusCode, $expectedFindResultsCount)
    {
        $this->client = self::createAdminAuthorizedClient('api');

        $this->mockRabbitmqProducer($this->client, 'araneum.base.rabbitmq.producer.spot');

        $this->client->request('POST', '/agent/api/spot/request ', ['filters' => $filters]);

        $response = $this->client->getResponse();

        $this->assertEquals(
            $expectedStatusCode,
            $response->getStatusCode(),
            $response->getContent()
        );

        $this->assertCount($expectedFindResultsCount, (json_decode($response->getContent(), true)));
    }
}
