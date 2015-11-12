<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Controller;

use Araneum\Base\Tests\Controller\BaseController;
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
     * Set of arguments for testEdit method
     *
     * @return array
     */
    public static function dataSource()
    {
        return [
            'Check unique username' => [
                'authLogin' => 'emptyuser',
                'username' => 'emailuser',
                'email' => 'emptyuser@araneum.dev',
                '__expected_value' => false,
            ],
            'Try username edit' => [
                'authLogin' => 'emptyuser',
                'username' => 'new_emptyuser',
                'email' => 'emptyuser@araneum.dev', //TODO попытка проапдейтить существующим уникальным значением разобратся https://github.com/symfony/symfony/issues/6651
                '__expected_value' => false,
            ],
            'Try short username' => [
                'authLogin' => 'new_emptyuser',
                'username' => '1',
                'email' => 'emptyuser@araneum.dev',
                '__expected_value' => false,
            ],
            'Try empty email' => [
                'authLogin' => 'new_emptyuser',
                'username' => 'new_emptyuser',
                'email' => '',
                '__expected_value' => false,
            ],
            'Try not valid email' => [
                'authLogin' => 'new_emptyuser',
                'username' => 'new_emptyuser',
                'email' => 'emptyuserraraneum@.dev',
                '__expected_value' => false,
            ],
            'Check unique email' => [
                'authLogin' => 'new_emptyuser',
                'username' => 'new_emptyuser',
                'email' => 'emailuser@araneum.dev',
                '__expected_value' => false,
            ]
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
     * Test for Password recovery link
     *
     * @runInSeparateProcess
     */
    public function testRecovery()
    {
        $client = $this->createAdminAuthorizedClient();
        $router = $client->getContainer()->get('router');

        $crawler = $client->request(
            'GET',
            $router->generate('sonata_admin_dashboard', ['_locale' => 'en'])
        );

        $crawler = $client->request(
            'GET',
            $router->generate('araneum_user_user_profileShow', ['_locale' => 'en'])
        );

        $link = $crawler->selectLink('Forgot password?')->link();

        $this->assertEquals($router->match($this->getUrl($link))['_route'], 'fos_user_resetting_request');
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
        $client = $this->createAdminAuthorizedClient();

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