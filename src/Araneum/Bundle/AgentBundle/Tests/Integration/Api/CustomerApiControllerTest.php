<?php

namespace Araneum\Bundle\AgentBundle\Test\Integration\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CustomerApiControllerTest
 *
 * @package Araneum\Bundle\AgentBundle\Test\Integration\Api
 */
class CustomerApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $customerLogin  = '/agent/api/customers/login/';

    /**
     * @return array
     */
    public function loginDataProvider()
    {
        return [
            'normal' => [
                [
                    'email' => 'asgasg@mailforspam.com',
                    'password' => 'asd123',
                ],
                Response::HTTP_OK,
            ],
            'error' => [
                [
                    'email' => 'asgasg@mailforspam.com',
                    'password' => 'NotRealPassword',
                ],
                Response::HTTP_BAD_REQUEST,
            ],
        ];
    }

    /**
     * Test customer login action controller
     *
     * @dataProvider loginDataProvider
     * @runTestsInSeparateProcesses
     * @param array $post
     * @param int   $expected
     */
    public function testLoginAction(array $post, $expected)
    {
        $client = $this->createAdminAuthorizedClient('api');

        $application = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName('Ultratrade');

        $client->request(
            'POST',
            $this->customerLogin.$application->getAppKey(),
            $post
        );

        $response = $client->getResponse();
        $this->assertEquals(
            $expected,
            $response->getStatusCode(),
            $response->getContent()
        );
    }
}
