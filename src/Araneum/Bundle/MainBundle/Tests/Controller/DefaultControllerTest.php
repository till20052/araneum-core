<?php

namespace Araneum\Bundle\MainBundle\Tests\Controller;

use Araneum\Base\Tests\Controller\BaseController;

class DefaultControllerTest extends BaseController
{
    /**
     * Test main page
     */
    public function testIndex()
    {
        $client = $this->createAdminAuthorizedClient();

        $crawler = $client->request('GET', '/en/admin/dashboard');

        $this->assertTrue($crawler->filter('html:contains("Sonata Admin")')->count() > 0);
    }
}
