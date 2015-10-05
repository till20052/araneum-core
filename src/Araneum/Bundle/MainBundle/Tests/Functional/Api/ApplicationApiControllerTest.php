<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;

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
        $client = self::createAdminAuthorizedClient();

        $client->request(
            'GET',
            $this->configGetUri . ApplicationFixtures::TEST_APP_API_KEY
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertEquals($response->getStatusCode(), 200);
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue(isset($decoded['id']));
    }
}