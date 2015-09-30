<?php
namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin\User;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Bundle\UserBundle\Tests\Functional\Utils\Data\UserManager;

class UserAdminTest extends BaseAdminController
{
    protected $createRoute = 'admin_araneum_user_user_create';
    protected $listRoute   = 'admin_araneum_user_user_list';
    protected $deleteRoute = 'admin_araneum_user_user_delete';
    protected $updateRoute = 'admin_araneum_user_user_edit';

    /**
     * {@inheritdoc}
     */
    public function filterDataSource()
    {
        $user = self::createClient()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')->findOneBy(['email' => 'filterTestAdminUser@test.com']);

        return
            [
                [
                    [
                        'filter[email][value]' => 'filterTestAdminUser',
                        'filter[enabled][value]' => false,
                        'filter[createdAt][value][start]' => '24/08/1979',
                        'filter[createdAt][value][end]' => '24/08/2015',
                    ],
                    true,
                    $user,
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function createDataSource()
    {
        return
            [
                [
                    [
                        'email' => 'testUserAdminCreate@test.com',
                        'username' => 'testUserAdminNameCreate',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    true
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateDataSource()
    {
        $user = static::createClient()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')->findOneBy(['username' => 'userAdminTestForUpdate']);

        return
            [
                [
                    [
                        'email' => 'userAdminTestAfterUpdate@test.com',
                        'username' => 'userAdminTestAfterUpdate',
                        'fullName' => 'TestFullName',
                    ],
                    true,
                    $user,
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDataSource()
    {
        $user = self::createClient()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')->findOneBy(['username' => 'userAdminTestForDelete']);

        return $user;
    }

    // USE FIXTURES INSTEAD !!!
    public static function setUpBeforeClass()
    {
        $manager = self::createClient()->getContainer()->get('doctrine.orm.entity_manager');

        UserManager::create(
            $manager,
            [
                'email' => 'filterTestAdminUser@test.com',
                'username' => 'filterTestAdminUserUserName',
                'fullName' => 'filterTestAdminUserFullName',
                'enabled' => false,
                'createdAt' => new \DateTime('1980-11-25'),
            ]
        );
        UserManager::create(
            $manager,
            [
                'email' => 'AlreadyExist@test.com',
                'username' => 'AlreadyExistName'
            ]
        );
        UserManager::create(
            $manager,
            [
                'username' => 'userAdminTest',
                'email' => 'test2test@test.com',
            ]
        );
        UserManager::create(
            $manager,
            [
                'username' => 'userAdminTestForUpdate',
                'email' => 'test2testForUpdate@test.com',
            ]
        );
        UserManager::create(
            $manager,
            [
                'username' => 'userAdminTestForDelete',
                'email' => 'testForDelete@test.com',
            ]
        );
    }

    public static function tearDownAfterClass()
    {
        $manager = self::createClient()->getContainer()->get('doctrine.orm.entity_manager');
        UserManager::delete(
            $manager,
            [
                'email' => 'testUserAdminCreate@test.com',
                'username' => 'testUserAdminNameCreate',
            ]
        );
        UserManager::delete(
            $manager,
            [
                'username' => 'userAdminTestAfterUpdate',
            ]
        );
    }
}