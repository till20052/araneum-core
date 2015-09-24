<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class ConnectionAdminTest extends BaseController
{
    /**
     * @runInSeparateProcess
     */
    public function testCreateAction()
    {
        $client = $this->createAdminAuthorizedClient();

        $crawler = $client->request('GET', '/en/admin/araneum/main/connection/create');

        $form = $crawler->selectButton('btn_create_and_edit')->form([
            '_type' => '1',
            '_name' => 'testHost',
            '_host' => 'testhost',
            '_port' => '3333',
            '_username' => 'testuser',
            '_enabled' => true,
            '_password' => '123456'
        ], 'POST');

        $crawler = $client->submit($form);

        $this->assertTrue($crawler->filter('html:contains("has been successfully created")')->count() > 0);
    }

    /**
     * @runInSeparateProcess
     */
    public function testListAction()
    {
        $client = $this->createAdminAuthorizedClient();

        $crawler = $client->request('GET', '/en/admin/araneum/main/connection/list');

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("Actions")')->count() > 0);
    }
}