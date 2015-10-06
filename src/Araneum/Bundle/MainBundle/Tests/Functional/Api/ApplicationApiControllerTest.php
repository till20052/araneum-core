<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

class ApplicationApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $configGetUri = '/api/application/config/';

    /**
     * Test ApplicationApiController
     *
     * @runInSeparateProcess
     */
    public function testGet()
    {
        $client = self::createAdminAuthorizedClient('api');

        $client->request(
            'GET',
            $this->configGetUri . ApplicationFixtures::TEST_APP_API_KEY
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue(isset($decoded['id']));
        $this->assertTrue(isset($decoded['name']));
        $this->assertTrue(isset($decoded['aliases']));
        $this->assertTrue(isset($decoded['domain']));
        $this->assertTrue(isset($decoded['public']));
        $this->assertTrue(isset($decoded['enabled']));
        $this->assertTrue(isset($decoded['status']));
        $this->assertTrue(isset($decoded['template']));
        $this->assertTrue(isset($decoded['db']));
        $this->assertTrue(isset($decoded['locale']));
        $this->assertTrue(isset($decoded['components']));
        $this->assertTrue(isset($decoded['cluster']));
        $this->assertTrue(isset($decoded['owner']));
        $this->assertTrue(is_int($decoded['id']));
        $this->assertTrue(is_string($decoded['name']));
        $this->assertTrue(is_string($decoded['aliases']));
        $this->assertTrue(is_string($decoded['domain']));
        $this->assertTrue(is_bool($decoded['public']));
        $this->assertTrue(is_bool($decoded['enabled']));
        $this->assertTrue(is_int($decoded['status']));
        $this->assertTrue(is_string($decoded['template']));
        $this->assertTrue(is_array($decoded['db']));
        $this->assertTrue(is_array($decoded['locale']));
        $this->assertTrue(is_array($decoded['components']));
        $this->assertTrue(is_array($decoded['cluster']));
        $this->assertTrue(is_array($decoded['owner']));
    }
}