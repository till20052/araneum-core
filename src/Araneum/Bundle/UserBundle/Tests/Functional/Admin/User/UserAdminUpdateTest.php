<?php
//
//namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin\User;
//
//use Araneum\Base\Tests\Controller\BaseAdminController;
//use Araneum\Base\Tests\Controller\BaseController;
//use Araneum\Bundle\UserBundle\Entity\User;
//use Araneum\Bundle\UserBundle\Tests\Functional\Utils\Data\UserManager;
//use Symfony\Bundle\FrameworkBundle\Client;
//
//class UserAdminUpdateTest extends BaseAdminController
//{
//    protected $updateRoute = 'admin_araneum_user_user_edit';
//
//    /**
//     * Test main page
//     *
//     * @dataProvider saveProvider
//     * @runInSeparateProcess
//     */
//    public function testUpdate(array $formInput, $expects, $user)
//    {
//        $this->baseTestUpdate($formInput, $expects, $user);
//    }
//
//    public function saveProvider()
//    {
//        $container = static::createClient()->getContainer();
//        $user = $container->get('doctrine.orm.entity_manager')
//            ->getRepository('AraneumUserBundle:User')->findOneBy(['username' => 'userAdminTest']);
//
//        return [
//            [
//                [
//                    'email' => 'testUserAdmin@test.com',
//                    'username' => 'userAdminTest12',
//                    'fullName' => 'TestFullName',
//                ],
//                true,
//                $user,
//            ]
//
//            //            [
//            //                'AlreadyExist@test.com',
//            //                'userAdminTest',
//            //                'pswd',
//            //                false,
//            //            ],
//            //            [
//            //                'testUserAdmin@test.com',
//            //                'userAdminTest',
//            //                'pswd',
//            //                false,
//            //            ],
//        ];
//    }
//
//    public static function setUpBeforeClass()
//    {
//        $client = static::createClient();
//        UserManager::create(
//            $client->getContainer()->get('doctrine.orm.entity_manager'),
//            ['username' => 'userAdminTest']
//        );
//        UserManager::create(
//            $client->getContainer()->get('doctrine.orm.entity_manager'),
//            ['email' => 'AlreadyExist@test.com']
//        );
//    }
//
//    public static function tearDownAfterClass()
//    {
//        $client = static::createClient();
//        UserManager::delete(
//            $client->getContainer()->get('doctrine.orm.entity_manager'),
//            ['username' => 'userAdminTest']
//        );
//    }
//}
