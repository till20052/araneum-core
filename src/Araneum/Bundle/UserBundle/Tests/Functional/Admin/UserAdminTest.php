<?php
namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Araneum\Bundle\UserBundle\Entity\Role;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

class UserAdminTest extends BaseAdminController
{
    protected $createRoute = 'admin_araneum_user_user_create';
    protected $listRoute   = 'admin_araneum_user_user_list';
    protected $deleteRoute = 'admin_araneum_user_user_delete';
    protected $updateRoute = 'admin_araneum_user_user_edit';


    /**
     * Set up Before class
     */
    public static function setUpBeforeClass()
    {
        $client = static::createClient();
        $manager = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $repository = $manager
            ->getRepository('AraneumUserBundle:User');

        $create = $repository->findOneBy(['email'=>'testUserAdminCreate@test.com']);

        $delete = $repository->findOneBy(['username' => UserFixtures::TEST_USER_NAME_DELETE]);

        $update = $repository->findOneBy(['email' => 'userAdminTestAfterUpdate@test.com']);

        if($create){
            $manager->remove($create);
            $manager->flush();
        }

        if($update){
            $manager->remove($update);
            $manager->flush();
        }

        if(!$delete){
            $role = $manager->getRepository('AraneumUserBundle:Role')->findOneBy(['name'=>'ROLE_USER']);


            $delete = new User();
            $delete
                ->setUsername(UserFixtures::TEST_USER_NAME_DELETE)
                ->setEmail('test@test.delete.email')
                ->setFullName('123')
                ->setPassword('112dfsdgfsd')
                ->setRoles([$role]);

            $manager->persist($delete);
            $manager->flush();
        }

    }
        /**
     * {@inheritdoc}
     */
    public function filterDataSource()
    {
        $manager =static::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $user = $manager
            ->getRepository('AraneumUserBundle:User')
            ->findOneBy(['email' => UserFixtures::TEST_USER_EMAIL_FILTER]);

        if(!$user){
            $user = new User();
            $user
                ->setUsername(UserFixtures::TEST_USER_NAME.'filter')
                ->setPassword('123sdfsdgdf')
                ->setEnabled(UserFixtures::TEST_USER_ENABLED_FILTER)
                ->setFullName(UserFixtures::TEST_USER_FULLNAME_FILTER)
                ->setEmail(UserFixtures::TEST_USER_EMAIL_FILTER);

            $manager->persist($user);
            $manager->flush();
        }

        return
            [
                'fullname and email' => [
                    [
                        'filter[fullName][value]' => UserFixtures::TEST_USER_FULLNAME_FILTER,
                        'filter[email][value]' => UserFixtures::TEST_USER_EMAIL_FILTER,
                    ],
                    true,
                    $user,
                ],
                'by part of fullname, email and by date' => [
                    [
                        'filter[email][value]' => substr(UserFixtures::TEST_USER_EMAIL_FILTER, 15),
                        'filter[fullName][value]' => substr(UserFixtures::TEST_USER_FULLNAME_FILTER, 15),
                        'filter[enabled][value]' => UserFixtures::TEST_USER_ENABLED_FILTER,
                        'filter[createdAt][value][start]' => '08/24/1979',
                        'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400),
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
                        'plainPassword' => 'pAs$w0rd',
                    ],
                    true
                ],
                'exist email' => [
                    [
                        'email' => UserFixtures::TEST_USER_NAME,
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pAs$w0rd',
                    ],
                    false
                ],
                'exist username' => [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => UserFixtures::TEST_USER_NAME,
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pAs$w0rd',
                    ],
                    false
                ],
                'not valid email' => [
                    [
                        'email' => 'nonValidEmail',
                        'username' => 'testUserAdminNameCreateUniq',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pAs$w0rd',
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
                        'plainPassword' => 'pAs$w0rd',
                    ],
                    false
                ],
                'empty username' => [
                    [
                        'email' => 'testUserAdminCreateUniq@test.com',
                        'username' => '',
                        'fullName' => 'TestFullName',
                        'plainPassword' => 'pAs$w0rd',
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
        $manager =static::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager');

        $user = $manager
            ->getRepository('AraneumUserBundle:User')
            ->findOneBy(['username' => UserFixtures::TEST_USER_NAME_UPDATE]);

        if(!$user){
            $user = new User();
            $user
                ->setUsername(UserFixtures::TEST_USER_NAME_UPDATE)
                ->setPassword('123sdfsdgdf')
                ->setFullName('TestFullName')
                ->setEmail(UserFixtures::TEST_USER_EMAIL_UPDATE);

            $manager->persist($user);
            $manager->flush();

        }

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