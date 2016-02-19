<?php
namespace Araneum\Bundle\MainBundle\Tests\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Controller\LdapApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * LdapApiControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service;
 */
class LdapApiControllerTest extends BaseController
{
    /**
     * @var Client
     */
    private $client;

    /**
     * Test Ldap Synchronization Controller
     * @runInSeparateProcess
     *
     */
    public function testLdapSynchronization()
    {
        $this->client->request(
            'GET',
            '/api/ldap/users/'
        );
        $response = $this->client->getResponse();
        $content = $response->getContent();
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'));
        $this->assertEquals(
            Response::HTTP_OK,
            $response->getStatusCode(),
            $content
        );

        $decoded = json_decode($content, true);
        $this->assertInternalType('array', $decoded);
        $this->assertEquals(2, count($decoded));
    }

    /**
     * Initialize requirements
     */
    protected function setUp()
    {
        $this->client = self::createAdminAuthorizedClient('admin');
    }
}
