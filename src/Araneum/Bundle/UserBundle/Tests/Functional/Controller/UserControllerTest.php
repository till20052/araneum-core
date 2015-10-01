<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

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
     * Set of arguments for testEdit method
     *
     * @return array
     */
    public static function dataSource()
    {
        return [
            [
                // Unique username
                'authLogin' => 'admin',
                'username' => 'emailuser',
                'email' => 'admin@araneum.dev',
                '__expected_value' => false,
            ],
            [
                // Username update
                'authLogin' => 'admin',
                'username' => 'new_admin',
                'email' => 'admin@araneum.dev',
                '__expected_value' => true,
            ],
            [
                // Too short profile name
                'authLogin' => 'new_admin',
                'username' => '1',
                'email' => 'admin@araneum.dev',
                '__expected_value' => false,
            ],
            [
                // Empty email
                'authLogin' => 'new_admin',
                'username' => 'admin_123',
                'email' => '',
                '__expected_value' => false,
            ],
            [
                // Non-email
                'authLogin' => 'new_admin',
                'username' => 'admin_123',
                'email' => 'emptyuserraraneum@.dev',
                '__expected_value' => false,
            ],
            [
                // Unique email
                'authLogin' => 'new_admin',
                'username' => 'admin_123',
                'email' => 'emptyuser@araneum.dev',
                '__expected_value' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function setUpBeforeClass()
    {
        self::bootKernel();

        self::$manager = static::$kernel->getContainer()
            ->get('doctrine.orm.entity_manager');

        self::$repository = self::$manager->getRepository('AraneumUserBundle:User');
    }

    /**
     * Test for Edit profile
     *
     * @param string $authLogin
     * @param string $username
     * @param string $email
     * @param mixed  $expectedValue
     * @dataProvider dataSource
     * @runInSeparateProcess
     */
    public function testEdit($authLogin, $username, $email, $expectedValue)
    {
        $client = $this->createAdminAuthorizedClient($authLogin);

        $form = $client
            ->request(
                'GET',
                $client
                    ->getContainer()
                    ->get('router')
                    ->generate('araneum_user_user_profileShow')
            )
            ->selectButton('btn_update_profile')
            ->form();

        $form->setValues(
            [
                'araneum_user_form_profile[username]' => $username,
                'araneum_user_form_profile[email]' => $email,
            ]
        );

        $this->assertEquals($expectedValue, count($client->submit($form)->filter('.alert-notice')) > 0);
    }
}