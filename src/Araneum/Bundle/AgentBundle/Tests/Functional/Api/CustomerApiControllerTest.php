<?php

namespace Araneum\Bundle\AgentBundle\Test\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Client;
use Araneum\Base\Tests\Fixtures\Agent\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerApiControllerTest
 *
 * @package Araneum\Bundle\AgentBundle\Test\Functional\Api
 */
class CustomerApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $customerInsert        = '/agent/api/customers/insert/';
    protected $customerResetPassword = '/agent/api/customers/reset_password';
    protected $customerLogin         = 'agent/api/customers/login/';

    protected $handlerService;

    /**
     * Settings up
     */
    public static function setUpBeforeClass()
    {
        $customer = static::createClient();
        $manager = $customer
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $repository = $manager
            ->getRepository('AraneumAgentBundle:Customer');

        $delete = $repository->findOneBy(['email' => 'email@email.com']);

        if ($delete) {
            $manager->remove($delete);
            $manager->flush();
        }
    }

    /**
     * Test customer controller
     *
     * @dataProvider                apiDataProvider
     * @runInSeparateProcess
     * @param                       array $post
     * @param                       int   $expectedCode
     */
    public function testPostCustomer(array $post, $expectedCode)
    {
        $client = self::createAdminAuthorizedClient('api');

        $this->mockHandlerService($client);

        $client->request(
            'POST',
            $this->customerInsert.ApplicationFixtures::TEST_APP_APP_KEY,
            $post
        );

        $response = $client->getResponse();

        $this->assertEquals($expectedCode, $response->getStatusCode(), $response->getContent());
    }

    /**
     * Test reset password customer method
     *
     * @param array $requestData
     * @param int   $expectedCode
     * @dataProvider resetPasswordDataSource
     * @runInSeparateProcess
     */
    public function testResetPassword(array $requestData, $expectedCode)
    {
        $client = self::createAdminAuthorizedClient('api');

        $this->mockHandlerService($client);

        $client->request(
            'POST',
            $this->customerResetPassword.'?app_key='.ApplicationFixtures::TEST_APP_APP_KEY,
            $requestData
        );
        $response = $client->getResponse();

        $this->assertEquals($expectedCode, $response->getStatusCode(), $response->getContent());
    }

    /**
     * ResetPassword data provider
     *
     * @return array
     */
    public function resetPasswordDataSource()
    {
        return [
            'normal' => [
                [
                    'password' => 'password',
                    'customer_id' => 123,
                    'email' => CustomerFixtures::TEST_2_EMAIL,
                ],
                Response::HTTP_OK,
            ],
            'short password' => [
                [
                    'password' => '1',
                    'customer_id' => 123,
                    'email' => CustomerFixtures::TEST_2_EMAIL,
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'not linked customer to application' => [
                [
                    'password' => '123456',
                    'customer_id' => 123,
                    'email' => CustomerFixtures::TEST_RESET_EMAIL,
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
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
                    'firstName' => 'ашкыТфьу',
                    'lastName' => 'lastName',
                    'country' => 1,
                    'email' => 'testEmail'.sha1(rand()).'@email.com',
                    'currency' => 'usd',
                    'phone' => '380993222234',
                    'siteId' => 3,
                    'birthday' => '2020-11-11',
                    'password' => '1234567',
                ],
                Response::HTTP_CREATED,
            ],
            'testFailCreate' => [
                [
                    'firstName' => CustomerFixtures::TEST_FIRST_NAME,
                    'lastName' => CustomerFixtures::TEST_LAST_NAME,
                    'country' => 12,
                    'email' => CustomerFixtures::TEST_EMAIL,
                    'currency' => CustomerFixtures::TEST_CURRENCY,
                    'phone' => CustomerFixtures::TEST_PHONE,
                    'birthday' => CustomerFixtures::TEST_BIRTHDAY,
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'normal fullName&lastName cirilica letters' => [
                [
                    'firstName' => "Дим'аЁ",
                    'lastName' => "Дим'аЁ",
                    'country' => 2,
                    'email' => 'testEmail'.sha1(rand()).'@email.com',
                    'currency' => 'usd',
                    'siteId' => 3,
                    'phone' => '380993222234',
                    'birthday' => '2222-12-12',
                    'password' => '1234567',
                ],
                Response::HTTP_CREATED,
            ],
        ];
    }

    /**
     * Test login
     *
     * @param array $requestData
     * @param int   $expectedCode
     * @dataProvider loginDataSource
     */
    public function testLogin(array $requestData, $expectedCode)
    {
        $client = self::createAdminAuthorizedClient('api');

        $this->mockHandlerService($client);

        $client->request(
            'POST',
            $this->customerLogin.ApplicationFixtures::TEST_APP_APP_KEY,
            $requestData
        );
        $response = $client->getResponse();

        $this->assertEquals($expectedCode, $response->getStatusCode(), $response->getContent());
    }

    /**
     * Login data provider
     *
     * @return array
     */
    public function loginDataSource()
    {
        return [
            'normal' => [
                [
                    'email' => CustomerFixtures::TEST_2_EMAIL,
                    'password' => 'mustBeMoreThat6',
                ],
                Response::HTTP_NO_CONTENT,
            ],
            'not linked customer and application' => [
                [
                    'email' => CustomerFixtures::TEST_RESET_EMAIL,
                    'password' => 'mustBeMoreThat6',
                ],
                Response::HTTP_NOT_FOUND,
            ],
            'short password' => [
                [
                    'email' => CustomerFixtures::TEST_2_EMAIL,
                    'password' => '1',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
            'short email' => [
                [
                    'email' => '1',
                    'password' => '123456',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }

    /**
     * Mock Customer Api Handler
     * @param Client $client
     */
    public function mockHandlerService(Client $client)
    {
        $spotOptionService = $this->getMock(
            '\Araneum\Bundle\AgentBundle\Service\SpotOptionService',
            [],
            [
                $this->getMockBuilder('\Araneum\Base\Service\RabbitMQ\ProducerService')->disableOriginalConstructor()->getMock(),
                $client->getContainer()->get('araneum.base.rabbitmq.producer.spot'),
                $client->getContainer()->get('araneum.base.spot_api'),
                $client->getContainer()->get('doctrine.orm.entity_manager'),
            ]
        );

        $client->getContainer()->set('araneum.agent.spotoption.service', $spotOptionService);

        $handlerMock = $this->getMock(
            '\Araneum\Bundle\AgentBundle\Service\CustomerApiHandlerService',
            ['createCustomerEvent'],
            [
                $client->getContainer()->get('araneum.main.application.manager'),
                $client->getContainer()->get('doctrine.orm.entity_manager'),
                $client->getContainer()->get('event_dispatcher'),
                $client->getContainer()->get('form.factory'),
                $client->getContainer()->get('araneum.agent.spotoption.service'),
            ]
        );

        $client->getContainer()->set('araneum.agent.customer.api_handler', $handlerMock);
    }
}
