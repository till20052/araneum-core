<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Fixtures\User\UserFixtures;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginPageTest
 *
 * @package Araneum\Bundle\UserBundle\Tests\Functional\Admin
 */
class LoginPageTest extends WebTestCase
{
    /**
     * Test for login Page
     *
     * @param string $user
     * @param string $password
     * @param mixed  $expected
     * @dataProvider dataSource
     */
    public function testLoginPage($user, $password, $expected)
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );

        $client = static::createClient();
        $router = $client->getContainer()->get('router');
        $crawler = $client->request('GET', $router->generate('fos_user_security_login', ['_locale' => 'en']));
        $form = $crawler->selectButton('Sign in')->form();
        $form['_username'] = $user;
        $form['_password'] = $password;
        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();
        $this->assertEquals($expected, count($crawler->filter('title:contains("Admin")')) <= 0);
    }

    /**
     * Set of arguments for testEdit method
     *
     * @return array
     */
    public static function dataSource()
    {
        return [
            'Check bad credentials' => [
                '_username' => 'admin',
                '_password' => 'qwerty',
                '__expected_value' => true,
            ],
            'Check login' => [
                '_username' => UserFixtures::ADMIN_USER_NAME,
                '_password' => UserFixtures::ADMIN_USER_PASSWORD,
                '__expected_value' => false,
            ],
        ];
    }
}
