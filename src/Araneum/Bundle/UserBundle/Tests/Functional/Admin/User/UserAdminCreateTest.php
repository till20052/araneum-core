<?php
//namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin\User;
//
//use Araneum\Base\Tests\Controller\BaseAdminController;
//use Araneum\Base\Tests\Controller\BaseController;
//use Araneum\Bundle\UserBundle\Tests\Functional\Utils\Data\UserManager;
//
//class UserAdminCreateTest extends BaseAdminController
//{
//    protected $createRoute = 'admin_araneum_user_user_create';
//
//    /**
//     * Test main page
//     *
//     * @dataProvider saveProvider
//     * @runInSeparateProcess
//     */
//    public function testCreate($formInput, $expects)
//    {
//        $this->baseTestCreate($formInput, $expects);
//    }
//
//    public function saveProvider()
//    {
//        return [
//            [
//                [
//                    'email' => 'testUserAdmin@test.com',
//                    'username' => 'testUserAdminName',
//                    'fullName' => 'TestFullName',
//                    'plainPassword' => 'pswd',
//                ],
//                true
//            ],
//            //            [
//            //                'AlreadyExist@test.com',
//            //                'testUserAdminName',
//            //                'pswd',
//            //                false
//            //            ],
//            //            [
//            //                'testUserAdmin@test.com',
//            //                'AlreadyExistName',
//            //                'pswd',
//            //                false
//            //            ],
//            //            [
//            //                'nonValidEmail',
//            //                'testUserAdminName',
//            //                'pswd',
//            //                false
//            //            ],
//            //            [
//            //                'testUserAdmin@test.com',
//            //                'testUserAdminName',
//            //                '1',
//            //                false
//            //            ],
//        ];
//    }
//
//    public static function setUpBeforeClass()
//    {
//        $client = self::createClient();
//        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');
//        UserManager::create(
//            $manager,
//            [
//                'email' => 'AlreadyExist@test.com',
//                'username' => 'AlreadyExistName'
//            ]
//        );
//        self::deleteTestUsers($manager);
//    }
//
//    public static function tearDownAfterClass()
//    {
//        $client = self::createClient();
//        $manager = $client->getContainer()->get('doctrine.orm.entity_manager');
//        UserManager::delete($manager, ['username' => 'AlreadyExistName']);
//        self::deleteTestUsers($manager);
//    }
//
//    /**
//     * @param $manager
//     */
//    private static function deleteTestUsers($manager)
//    {
//        UserManager::delete($manager, ['email' => 'testUserAdmin@test.com']);
//        UserManager::delete($manager, ['email' => 'uniqtestUserAdminName']);
//        UserManager::delete($manager, ['username' => 'testUserAdminName']);
//        UserManager::delete($manager, ['username' => 'uniqtestUserAdminName']);
//    }
//}