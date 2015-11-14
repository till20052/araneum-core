<?php

namespace Araneum\Bundle\AgentBundle\Test\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Customer\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

class CustomerApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $configGetUri = '/en/agent/api/customers/insert/' . ApplicationFixtures::TEST_APP_APP_KEY;


    public static function setUpBeforeClass()
    {
        $customer = static::createClient();
        $manager = $customer->getContainer()
            ->get('doctrine.orm.entity_manager');

        $repository = $manager
            ->getRepository('AraneumAgentBundle:Customer');

        $delete = $repository->findOneBy(['email'=>'email@email.com']);

        if($delete){
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
    public function testPutCustomer(array $post, $expected)
    {
        $client = self::createAdminAuthorizedClient('api');
        $client->request(
            'POST',
            $this->configGetUri,
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
            'testCreateCustomer' => [
                [
                    'firstName' => 'firstName',
                    'lastName' => 'lastName',
                    'country' => 'country',
                    'email' => 'email@email.com',
                    'callback' => true,
                    'phone' => '380993222234'
                ],
                Response::HTTP_CREATED
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
                Response::HTTP_BAD_REQUEST
            ]
        ];
    }
}