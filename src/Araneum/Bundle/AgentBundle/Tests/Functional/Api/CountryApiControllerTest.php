<?php

namespace Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CountryApiControllerTest
 *
 * @package Functional\Api
 */
class CountryApiControllerTest extends BaseController
{
    /**
     * @var string
     */
    private static $url = '/agent/api/get_countries/';

    /**
     * @var
     */
    private static $appKey;

    /**
     * @var
     */
    private $guzzleHttpResponseMock;

    /**
     * test Get Country
     *
     * @dataProvider apiDataProvider
     * @runInSeparateProcess
     * @param array $post
     * @param mixed $expected
     * @param int   $expectedCount
     */
    public function testGetCountry(array $post, $expected, $expectedCount)
    {
        $client = $this->createConfiguredMockedClient();

        $client->request(
            'GET',
            self::$url.self::$appKey,
            $post
        );

        $response = $client->getResponse();
        $this->assertEquals(
            $expected,
            $response->getStatusCode(),
            $response->getContent()
        );

        $this->assertGreaterThan(
            $expectedCount,
            count(json_decode($response->getContent(), true))
        );
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function apiDataProvider()
    {
        return [
            'normal' => [
                [
                    'MODULE' => 'Country',
                    'COMMAND' => 'view',
                ],
                Response::HTTP_OK,
                4,
            ],
            'operation_failed' => [
                [
                    'MODULE' => 'blabla',
                    'COMMAND' => 'rrr',
                ],
                Response::HTTP_OK,
                1,
            ],
        ];
    }

    /**
     * Settings up
     */
    public static function setUpBeforeClass()
    {
        $client = self::createClient();

        $manager = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $repository = $manager
            ->getRepository('AraneumMainBundle:Application');

        $entity = $repository->findOneByName(ApplicationFixtures::TEST_APP_NAME);

        self::$appKey = $entity->getAppKey();
    }

    /**
     * Create client with mocked guzzle and response
     *
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    private function createConfiguredMockedClient()
    {
        $this->markTestSkipped();

        $client = $this->createClientWithMockServices('api');

        $this->guzzleHttpResponseMock = $this
            ->getMockBuilder('\Guzzle\Http\Message\Response')
            ->disableOriginalConstructor()
            ->getMock();

        $this->guzzleHttpResponseMock
            ->expects($this->any())
            ->method('json')
            ->will(
                $this->returnValue(
                    [
                        'status' => [
                            'connection_status' => 'successful',
                            'operation_status' => 'successful',
                            'Country' => [
                                'data_1' => [],
                                'data_2' => [],
                                'data_3' => [],
                                'data_4' => [],
                                'data_5' => [],
                            ],
                        ],
                    ]
                )
            );

        $this->guzzleClientMock
            ->expects($this->any())
            ->method('post')
            ->will($this->returnValue($this->guzzleHttpRequestMock));

        $this->guzzleHttpRequestMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->guzzleHttpResponseMock));

        return $client;
    }
}
