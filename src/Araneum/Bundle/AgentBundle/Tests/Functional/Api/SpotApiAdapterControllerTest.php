<?php

namespace Araneum\Bundle\AgentBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Agent\LeadFixtures;
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
    public function findActionDataProvider()
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
            'Find 2 Leads' => [
                [
                    'phone' => substr(LeadFixtures::LEAD_FST_PHONE, 0, 6),
                ],
                Response::HTTP_OK,
                1,
            ],
        ];
    }

    /**
     * Test findAction in LeadApiController
     *
     * @dataProvider findActionDataProvider
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

        $this->client->request('GET', '/agent/api/lead/find', ['filters' => $filters]);

        $response = $this->client->getResponse();

        $this->assertEquals(
            $expectedStatusCode,
            $response->getStatusCode(),
            $response->getContent()
        );

        $this->assertCount($expectedFindResultsCount, (json_decode($response->getContent(), true)));
    }
}
