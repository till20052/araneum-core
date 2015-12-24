<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApplicationApiControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Api
 */
class ApplicationApiControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $configGetUri = '/api/application/config/';

    /**
     * Test ApplicationApiController
     */
    public function testGet()
    {
        $client = self::createAdminAuthorizedClient('api');

        $client->request(
            'GET',
            $this->configGetUri.ApplicationFixtures::TEST_APP_APP_KEY
        );
        $response = $client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertArrayStructureEquals($this->getArrayStructure(), $decoded);
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
            'owner' => 'array',
        ];
    }

    /**
     * @param $expectedStructure
     * @param $actual
     */
    private function assertArrayStructureEquals($expectedStructure, $actual)
    {
        foreach ($expectedStructure as $fieldName => $fieldType) {
            $this->assertTrue(isset($actual[$fieldName]));
            if ($fieldType == 'int') {
                $this->assertTrue(is_int($actual[$fieldName]));
            }
            if ($fieldType == 'string') {
                $this->assertTrue(is_string($actual[$fieldName]));
            }
            if ($fieldType == 'bool') {
                $this->assertTrue(is_bool($actual[$fieldName]));
            }
            if ($fieldType == 'array') {
                $this->assertTrue(is_array($actual[$fieldName]));
            }
        }
    }
}
