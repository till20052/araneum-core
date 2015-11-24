<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DomCrawler\Link;
use Symfony\Component\HttpFoundation\Response;

class AdminUserControllerTest extends BaseController
{
    /**
     * @var EntityManager
     */
    private static $manager;

    /**
     * @var EntityRepository
     */
    private static $repository;

    /**
     * @var user settings
     */
    private static $settings;

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::bootKernel();

        self::$manager = static::$kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        self::$repository = self::$manager->getRepository('AraneumUserBundle:User');

        self::$settings = [
            'Param1' => 'value1',
            'Param2' => 'value2'
        ];
    }

    /**
     * Test for set
     */
    public function testSettingsSet()
    {
        $client = $this->createAdminAuthorizedClient(UserFixtures::ADMIN_USER_NAME);

        $router = $client
            ->getContainer()
            ->get('router');

        $client->request(
            'POST',
            $router->generate('araneum_user_adminUser_setSettings'),
            self::$settings
        );

        $response = $client->getResponse();

        $this->assertEquals(
            Response::HTTP_ACCEPTED,
            $response->getStatusCode()
        );
    }
}