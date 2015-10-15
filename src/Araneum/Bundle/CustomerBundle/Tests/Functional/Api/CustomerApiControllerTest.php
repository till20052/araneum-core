<?php

namespace Araneum\Bundle\CustomerBundle\Test\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Customer\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

class CustomerApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $configGetUri = '/en/api/customers/data?appKey=' . ApplicationFixtures::TEST_APP_APP_KEY;

    /**
     * Test customer controller
     *
     * @dataProvider apiDataProvider
     * @runTestsInSeparateProcesses
     * @param array $post
     * @param int   $expected
     */
    public function testPutCustomer(array $post, $expected)
    {
        $client = self::createAdminAuthorizedClient('api');
        $client->request(
            'POST',
            $this->configGetUri,
            $post
        );

        $response = $client->getResponse();
        $this->assertEquals($expected, $response->getStatusCode());
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function apiDataProvider()
    {
        return [
            'testCreateCustomer' => [
                [
                    'firstName' => 'firstName',
                    'lastName' => 'lastName',
                    'country' => 'country',
                    'email' => 'email@email.com',
                    'callback' => true,
                    'phone' => '322223'
                ],
                Response::HTTP_OK
            ],
            'testFailCreate' => [
                [
                    'firstName' => CustomerFixtures::TEST_FIRST_NAME,
                    'lastName' => CustomerFixtures::TEST_LAST_NAME,
                    'country' => CustomerFixtures::TEST_COUNTRY,
                    'email' => CustomerFixtures::TEST_EMAIL,
                    'callback' => true,
                    'phone' => CustomerFixtures::TEST_PHONE
                ],
                Response::HTTP_INTERNAL_SERVER_ERROR
            ]
        ];
    }
}