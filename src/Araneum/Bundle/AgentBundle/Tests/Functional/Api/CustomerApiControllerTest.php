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
     * @dataProvider apiDataProvider
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
                    'birthday' => '2020-11-11',
                    'password' => '1234567',
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
                    'password' => '1234567',
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
}
