<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Api;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TranslatesApiGetTest
 * @package Araneum\Bundle\MainBundle\Tests\Functional\Api
 */
class AdminDefaultControllerTest extends BaseController
{
    /**
     * @var string uri to call rest api method
     */
    protected $configGetUri = 'en/manage/translates.json';

    /**
     * Test AdminDefaultController
     */
    public function testGetTranslateList()
    {
        $client = self::createAdminAuthorizedClient('api');
        $client->request(
            'GET',
            $this->configGetUri
        );

        $response = $client->getResponse();
        $content = $response->getContent();
        $decoded = json_decode($content, true);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertTrue(isset($decoded['admin']['sidebar.WELCOME']));
        $this->assertTrue(is_string($decoded['admin']['sidebar.WELCOME']));
    }
}