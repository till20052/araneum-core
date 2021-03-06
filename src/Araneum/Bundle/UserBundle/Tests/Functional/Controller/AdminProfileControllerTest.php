<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminProfileControllerTest
 *
 * @package Araneum\Bundle\UserBundle\Tests\Functional\Controller
 */
class AdminProfileControllerTest extends BaseController
{
    /**
     * Test for set
     *
     * @runInSeparateProcess
     */
    public function testSettingsSet()
    {
        $client = $this->createAdminAuthorizedClient('admin');

        $router = $client
            ->getContainer()
            ->get('router');

        $client->request(
            'POST',
            $router->generate('araneum_user_adminUser_setSettings'),
            [
                'Param1' => 'value1',
                'Param2' => 'value2',
            ],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $response = $client->getResponse();

        $this->assertEquals(
            Response::HTTP_ACCEPTED,
            $response->getStatusCode(),
            $response->getContent()
        );
    }
}
