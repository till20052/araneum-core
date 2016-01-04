<?php

namespace Araneum\Bundle\AgentBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Agent\LeadFixtures;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LeadApiControllerTest
 *
 * @package Araneum\Bundle\AgentBundle\Tests\Functional\Controller
 */
class LeadApiControllerTest extends BaseController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $tempEmail;

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
                    'email' => LeadFixtures::LEAD_FST_EMAIL,
                    'phone' => LeadFixtures::LEAD_FST_PHONE,
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
        $this->client->request('GET', '/agent/api/lead/find', ['filters' => $filters]);

        $response = $this->client->getResponse();

        $this->assertEquals(
            $expectedStatusCode,
            $response->getStatusCode(),
            $response->getContent()
        );

        $this->assertCount($expectedFindResultsCount, (json_decode($response->getContent(), true)));
    }

    /**
     * Data for test find action
     *
     * @return array
     */
    public function createActionDataProvider()
    {
        return [
            'Success' => [
                [
                    'firstName' => 'Hugo',
                    'lastName' => 'Boss',
                    'country' => rand(1, 239),
                    'email' => 'testLead'.rand(1, 999).'@test.com',
                    'phone' => '380507894561',
                    'appKey' => md5(microtime(true)),
                ],
                Response::HTTP_CREATED,
            ],
            'normal fullName&lastName cirilica letters' => [
                [
                    'firstName' => "Дим'аЁ",
                    'lastName' => "Дим'аЁ",
                    'country' => rand(1, 239),
                    'email' => 'testLead'.rand(1, 999).'@test.com',
                    'phone' => '380507894561',
                    'appKey' => md5(microtime(true)),
                ],
                Response::HTTP_CREATED,
            ],
            'Not valid email value' => [
                [
                    'firstName' => 'Calvin',
                    'lastName' => 'Klein',
                    'country' => rand(1, 239),
                    'email' => md5(microtime(true)),
                    'phone' => '380507894561',
                    'appKey' => md5(microtime(true)),
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'Not valid phone value' => [
                [
                    'firstName' => 'Tony',
                    'lastName' => 'Perotti',
                    'country' => rand(1, 239),
                    'email' => 'tony.perotti@test.com',
                    'phone' => md5(microtime(true)),
                    'appKey' => md5(microtime(true)),
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }

    /**
     * Test createAction in LeadApiController
     *
     * @dataProvider createActionDataProvider
     * @runInSeparateProcess
     *
     * @param array $data
     * @param int   $expected
     */
    public function testCreateAction($data, $expected)
    {
        $this->tempEmail = $data['email'];

        $this->client = self::createAdminAuthorizedClient('api');
        $this->client->request(
            'POST',
            '/agent/api/lead/create',
            $data
        );

        $response = $this->client->getResponse();

        $this->assertSame(
            $expected,
            $response->getStatusCode(),
            $response->getContent()
        );
    }
}
