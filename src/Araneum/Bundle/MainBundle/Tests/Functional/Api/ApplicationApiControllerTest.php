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
    protected $configGetUri = '/en/api/application/config/';

    /**
     * Test ApplicationApiController
     *
     *
     */
    public function testGet()
    {
        $client = self::createAdminAuthorizedClient('api');

        $client->request(
            'GET',
            $this->configGetUri . ApplicationFixtures::TEST_APP_APP_KEY
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $arrayStructure = $this->getArrayStructure();
        foreach ($arrayStructure as $k => $v) {
            $this->assertTrue(isset($decoded[$k]));
            if ($arrayStructure[$k] == 'int') {
                $this->assertTrue(is_int($decoded[$k]));
            }
            if ($arrayStructure[$k] == 'string') {
                $this->assertTrue(is_string($decoded[$k]));
            }
            if ($arrayStructure[$k] == 'bool') {
                $this->assertTrue(is_bool($decoded[$k]));
            }
            if ($arrayStructure[$k] == 'array') {
                $this->assertTrue(is_array($decoded[$k]));
            }
        }
    }

    /**
     * Get array structure for checked
     *
     * @return array
     */
    private function getArrayStructure()
    {
        return [
            'id' => 'int',
            'name' => 'string',
            'aliases' => 'string',
            'domain' => 'string',
            'public' => 'bool',
            'enabled' => 'bool',
            'status' => 'int',
            'template' => 'string',
            'db' => 'array',
            'locales' => 'array',
            'components' => 'array',
            'cluster' => 'array',
            'owner' => 'array'
        ];
    }
}