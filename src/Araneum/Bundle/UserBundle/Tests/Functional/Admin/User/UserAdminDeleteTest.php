<?php
//
//namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin\User;
//
//use Araneum\Base\Tests\Controller\BaseAdminController;
//use Araneum\Base\Tests\Controller\BaseController;
//use Araneum\Bundle\UserBundle\Tests\Functional\Utils\Data\UserManager;
//
//class UserAdminDeleteTest extends BaseAdminController
//{
//
//    protected $deleteRoute = 'admin_araneum_user_user_delete';
//
//    /**
//     * Before testing method for create user object and save $entityId to static var
//     *
//     * @BeforeClass
//     */
//    public static function setUpBeforeClass()
//    {
//        $client = static::createClient();
//        UserManager::create($client->getContainer()->get('doctrine.orm.entity_manager'));
//    }
//
//    /**
//     * Delete User test
//     *
//     * @runInSeparateProcess
//     */
//    public function testDeleteAction()
//    {
//        $user = self::createClient()->getContainer()->get('doctrine.orm.entity_manager')
//            ->getRepository('AraneumUserBundle:User')->findOneBy(['username' => 'testAdminUser']);
//
//        $this->baseTestDelete($user);
//    }
//}