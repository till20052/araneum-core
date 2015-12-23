<?php

namespace Araneum\Bundle\AgentBundle\Test\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerApiControllerTest
 *
 * @package Araneum\Bundle\AgentBundle\Test\Functional\Api
 */
class ErrorApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $errorUri = '/agent/api/errors/insert/';

    /**
     * Test customer controller
     *
     * @dataProvider apiDataProvider
     * @runTestsInSeparateProcesses
     * @param array $post
     * @param int   $expected
     */
    public function testPostError(array $post, $expected)
    {
        $client = self::createAdminAuthorizedClient('api');
        $client->request(
            'POST',
            $this->errorUri.ApplicationFixtures::TEST_APP_APP_KEY,
            $post
        );

        $response = $client->getResponse();

        $this->assertEquals(
            $expected,
            $response->getStatusCode(),
            $response->getContent()
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
            'Normal' => [
                [
                    'error_type' => 1,
                    'error_message' => 'Normal insert',
                    'error_sent_at' => '2015-12-22T14:05:21',
                ],
                Response::HTTP_CREATED,
            ],
            'Invalid error type' => [
                [
                    'error_type' => 12,
                    'error_message' => 'Invalid error type',
                    'error_sent_at' => '2015-12-22T14:05:21',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'Empty error type' => [
                [
                    'error_type' => '',
                    'error_message' => 'Empty error type',
                    'error_sent_at' => '2015-12-22T14:05:21',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'Empty error message' => [
                [
                    'error_type' => 2,
                    'error_message' => '',
                    'error_sent_at' => '2015-12-22T14:05:21',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'Empty error sent date' => [
                [
                    'error_type' => 2,
                    'error_message' => 'Empty error sent date',
                    'error_sent_at' => '',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }
}