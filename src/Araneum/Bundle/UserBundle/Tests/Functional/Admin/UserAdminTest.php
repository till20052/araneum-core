<?php
namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;

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
        $user = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')
            ->findOneBy(['email' => UserFixtures::TEST_USER_EMAIL_FILTER]);

        return
            [
                'fullname and email and createAt' => [
                    [
                        'filter[email][value]' => UserFixtures::TEST_USER_EMAIL_FILTER,
                        'filter[enabled][value]' => UserFixtures::TEST_USER_ENABLED_FILTER,
                        'filter[createdAt][value][start]' => '24/08/1979',
                        'filter[createdAt][value][end]' => '24/08/2015',
                    ],
                    true,
                    $user,
                ],
                'by part of fullname, email and by date' => [
                    [
                        'filter[email][value]' => substr(UserFixtures::TEST_USER_EMAIL_FILTER, 15),
                        'filter[fullName][value]' => substr(UserFixtures::TEST_USER_FULLNAME_FILTER, 15),
                        'filter[enabled][value]' => UserFixtures::TEST_USER_ENABLED_FILTER,
                        'filter[createdAt][value][start]' => '24/08/1979',
                        'filter[createdAt][value][end]' => '24/08/2015',
                    ],
                    true,
                    $user,
                ],
                'not exist entity' => [
                    [
                        'filter[email][value]' => 'NotExist',
                    ],
                    false,
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
                'normal' => [
                    [
                        'email' => 'testUserAdminCreate@test.com',
                        'username' => 'testUserAdminNameCreate',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    true
                ],
                'exist email' => [
                    [
                        'email' => UserFixtures::TEST_USER_NAME,
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                'exist username' => [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => UserFixtures::TEST_USER_NAME,
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                'not valid email' => [
                    [
                        'email' => 'nonValidEmail',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                'short password' => [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => '1',
                    ],
                    false
                ],
                'empty email' => [
                    [
                        'email' => '',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                'empty username' => [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => '',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateDataSource()
    {
        $user = static::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')
            ->findOneBy(['username' => UserFixtures::TEST_USER_NAME_UPDATE]);

        return
            [
                'normal' => [
                    [
                        'email' => 'userAdminTestAfterUpdate@test.com',
                        'username' => 'userAdminTestAfterUpdate',
                        'fullName' => 'TestFullName',
                    ],
                    true,
                    $user,
                ],
                'exist email' => [
                    [
                        'email' => UserFixtures::TEST_USER_EMAIL,
                        'fullName' => 'TestFullName',
                    ],
                    false,
                    $user,
                ],
                'exist username' => [
                    [
                        'username' => UserFixtures::TEST_USER_NAME,
                        'fullName' => 'TestFullName',
                    ],
                    false,
                    $user,
                ],
                'not valid email' => [
                    [
                        'email' => 'NOTVALID',
                        'fullName' => 'TestFullName',
                    ],
                    false,
                    $user,
                ],
            ];
    }

    /**
     * {@inheritdoc}
     */
    public function deleteDataSource()
    {
        return self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')
            ->findOneBy(['username' => UserFixtures::TEST_USER_NAME_DELETE]);
    }
}