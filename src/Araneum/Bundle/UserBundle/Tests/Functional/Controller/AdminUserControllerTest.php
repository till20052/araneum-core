<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DomCrawler\Link;

class UserControllerUpdateTest extends BaseController
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

        self::$manager = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        self::$repository = self::$manager->getRepository('AraneumUserBundle:User');

        self::$settings = [
            'Param1' => 'value1',
            'Param2' => 'value2'
        ];
    }

    /**
     * Test for set
     *
     * @runInSeparateProcess
     */
    public function testSet()
    {
    }

    /**
     * Test for Get Settings
     *
     * @runInSeparateProcess
     */
    public function testGet()
    {
        $client = $this->createAdminAuthorizedClient(UserFixtures::TEST_USER_NAME);

        $form = $client
            ->request(
                'GET',
                $client
                    ->getContainer()
                    ->get('router')
                    ->generate('araneum_user_get_settings')
            );
        //$this->assertEquals($expectedValue, count($client->submit($form)->filter('.alert-notice')) > 0);
    }

    /**
     * Get route from Url
     *
     * @param Link $link
     * @return mixed
     */
    public function getUrl(Link $link)
    {
        return parse_url($link->getUri())['path'];
    }
}