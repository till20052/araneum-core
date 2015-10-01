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
        $user = self::createClient()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')->findOneBy(['email' => UserFixtures::TEST_USER_EMAIL_FILTER]);

        return
            [
                [
                    [
                        'filter[email][value]' => UserFixtures::TEST_USER_EMAIL_FILTER,
                        'filter[enabled][value]' => UserFixtures::TEST_USER_ENABLED_FILTER,
                        'filter[createdAt][value][start]' => '24/08/1979',
                        'filter[createdAt][value][end]' => '24/08/2015',
                    ],
                    true,
                    $user,
                ],
                [
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
                [
                    [
                        'email' => UserFixtures::TEST_USER_NAME,
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => UserFixtures::TEST_USER_NAME,
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                [
                    [
                        'email' => 'nonValidEmail',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => '1',
                    ],
                    false
                ],
                [
                    [
                        'email' => '',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pswd',
                    ],
                    false
                ],
                [
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
        $user = static::createClient()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')->findOneBy(['username' => UserFixtures::TEST_USER_NAME_UPDATE]);

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
                [
                    [
                        'email' => UserFixtures::TEST_USER_EMAIL,
                        'fullName' => 'TestFullName',
                    ],
                    false,
                    $user,
                ],
                [
                    [
                        'username' => UserFixtures::TEST_USER_NAME,
                        'fullName' => 'TestFullName',
                    ],
                    false,
                    $user,
                ],
                [
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
        $user = self::createClient()->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumUserBundle:User')->findOneBy(
                ['username' => UserFixtures::TEST_USER_NAME_DELETE]
            );

        return $user;
    }
}