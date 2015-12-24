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
     * test Get Country
     *
     * @dataProvider apiDataProvider
     * @param array $post
     * @param mixed $expected
     * @param int   $expectedCount
     */
    public function testGetCountry(array $post, $expected, $expectedCount)
    {
        $this->markTestSkipped();

        $client = self::createAdminAuthorizedClient('api');
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
                10,
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

        $entity = $repository->findOneByName('Ultratrade');

        self::$appKey = $entity->getAppKey();
    }
}
