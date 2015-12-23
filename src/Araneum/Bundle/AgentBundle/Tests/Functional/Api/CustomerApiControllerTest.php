<?php

namespace Araneum\Bundle\AgentBundle\Test\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Customer\CustomerFixtures;
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
    protected $customerInsert = '/agent/api/customers/insert/';
    protected $customerLogin  = '/agent/api/customers/login/';
    protected $guzzleHttpResponseMock;

    /**
     * Settings up
     */
    public static function setUpBeforeClass()
    {
        $manager = static::createClient()
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
     * @dataProvider postCustomerDataProvider
     * @runTestsInSeparateProcesses
     * @param array $post
     * @param int   $expected
     */
    public function testPostCustomer(array $post, $expected)
    {
        $client = self::createAdminAuthorizedClient('api');
        $client->request(
            'POST',
            $this->customerInsert.ApplicationFixtures::TEST_APP_APP_KEY,
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
    public function postCustomerDataProvider()
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
                    'birthday' => '2020-11-11',
                ],
                Response::HTTP_CREATED,
            ],
            'normal fullName&lastName cirilica letters' => [
                [
                    'firstName' => "Дим'аЁ",
                    'lastName' => "Дим'аЁ",
                    'country' => 2,
                    'email' => 'testEmail'.sha1(rand()).'@email.com',
                    'currency' => 'usd',
                    'phone' => '380993222234',
                    'birthday' => '2222-12-12',
                ],
                Response::HTTP_CREATED,
            ],
            'testFailCreate' => [
                [
                    'firstName' => CustomerFixtures::TEST_FIRST_NAME,
                    'lastName' => CustomerFixtures::TEST_LAST_NAME,
                    'country' => CustomerFixtures::TEST_COUNTRY,
                    'email' => CustomerFixtures::TEST_EMAIL,
                    'currency' => CustomerFixtures::TEST_CURRENCY,
                    'phone' => CustomerFixtures::TEST_PHONE,
                    'birthday' => CustomerFixtures::TEST_BIRTHDAY,
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }

    /**
     * Data provider for login test
     *
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            'normal' => [
                [
                    'email' => CustomerFixtures::TEST_CUSTOMER_EMAIL,
                    'password' => '1234567',
                ],
                true,
                Response::HTTP_OK,
            ],
            'error' => [
                [
                    'email' => CustomerFixtures::TEST_CUSTOMER_EMAIL,
                    'password' => '1234567',
                ],
                false,
                Response::HTTP_BAD_REQUEST,
            ],
            'validation error short password' => [
                [
                    'email' => CustomerFixtures::TEST_CUSTOMER_EMAIL,
                    'password' => '1',
                ],
                true,
                Response::HTTP_BAD_REQUEST,
            ],
            'validation error short email' => [
                [
                    'email' => '1',
                    'password' => '123456',
                ],
                true,
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }

    /**
     * Test customer login action controller
     *
     * @dataProvider loginDataProvider
     * @runTestsInSeparateProcesses
     * @param array   $post
     * @param boolean $isSpotSuccessful parameters that define how spotOption response
     * @param int     $expected
     */
    public function testLoginAction(array $post, $isSpotSuccessful, $expected)
    {
        $client = $this->createConfiguredMockedClient($isSpotSuccessful);

        $client->request(
            'POST',
            $this->customerLogin.ApplicationFixtures::TEST_APP_APP_KEY,
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
     * Create client with mocked guzzle and response
     *
     * @param $isSpotSuccessful
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    private function createConfiguredMockedClient($isSpotSuccessful)
    {
        $customerId = 123;
        $session = 'd2925a4d5c856a6d09bc10c1f4f4ef51';

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
                        'status' => $isSpotSuccessful,
                        'customerId' => $customerId,
                    ]
                )
            );

        $this->guzzleHttpResponseMock
            ->expects($this->any())
            ->method('getSetCookie')
            ->will(
                $this->returnValue(
                    'spotsession_1_2142478985='.$session.'; path=/; domain=.spotplatform.ultratrade.com'
                )
            );

        $this->guzzleClientMock
            ->expects($this->any())
            ->method('createRequest')
            ->will($this->returnValue($this->guzzleHttpRequestMock));

        $this->guzzleHttpRequestMock
            ->expects($this->any())
            ->method('send')
            ->will($this->returnValue($this->guzzleHttpResponseMock));

        return $client;
    }
}
