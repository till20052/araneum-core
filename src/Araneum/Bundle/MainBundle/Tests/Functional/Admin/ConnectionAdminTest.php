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

        $form = $crawler->selectButton('btn_create_and_list')->form(
            [
                '_type' => '1',
                '_name' => 'testHost',
                '_host' => 'testhost',
                '_port' => '3333',
                '_username' => 'testuser',
                '_enabled' => true,
                '_password' => '123456'
            ], 'POST');

        key(array_slice($form->getPhpValues(), 1, 1));
        //$crawler = $client->submit($form);

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertTrue($crawler->filter('html:contains("has been successfully created")')->count() > 0);
    }
//
//    /**
//     * @runInSeparateProcess
//     */
//    public function testListAction()
//    {
//        $client = $this->createAdminAuthorizedClient();
//
//        $crawler = $client->request('GET', '/en/admin/araneum/main/connection/list');
//
//        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
//        $this->assertTrue($crawler->filter('html:contains("Actions")')->count() > 0);
//    }

//    /**
//     * Test for filters
//     * All filters not empty
//     *
//     * @runInSeparateProcess
//     */
//    public function testFilterAction()
//    {
//        $client = $this->createAdminAuthorizedClient();
//
//        $crawler = $client->request('GET', '/en/admin/araneum/main/connection/list');
//
//        $form = $crawler->selectButton('Filter')->form([
//            'filter[type][type]' => '1',
//            'filter[name][value]' => 'Test',
//            'filter[host][value]' => 'test',
//            'filter[port][value]' => '12',
//            'filter[userName][value]' => 'test',
//            'filter[enabled][value]' => '2',
//            'filter[createdAt][value][start]' => '01/01/1970',
//            'filter[createdAt][value][end]' => '01/01/2040',
//            'filter[updatedAt][value][start]' => '01/01/1970',
//            'filter[updatedAt][value][end]' => '01/01/2040',
//        ], 'GET');
//
//        $crawler = $client->submit($form);
//
//        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
//        $this->assertTrue($crawler->filter('html:contains("No result")')->count() > 0);
//    }
}