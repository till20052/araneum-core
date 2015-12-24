<?php

namespace Araneum\Base\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;

/**
 * Class BaseController
 *
 * @package Araneum\Base\Tests\Controller
 */
class BaseController extends WebTestCase
{
    /**
     * Return admin authorized client
     *
     * @param  string $authLogin
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected static function createAdminAuthorizedClient($authLogin = 'admin')
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $client->setServerParameters(['HTTP_HOST' => $container->getParameter('session_prefix')]);

        $session = $container->get('session');
        /**
         * @var $userManager \FOS\UserBundle\Doctrine\UserManager
         */
        $userManager = $container->get('fos_user.user_manager');
        /**
         * @var $loginManager \FOS\UserBundle\Security\LoginManager
         */
        $loginManager = $container->get('fos_user.security.login_manager');
        $firewallName = $container->getParameter('fos_user.firewall_name');

        $user = $userManager->findUserBy(['username' => $authLogin]);
        $loginManager->loginUser($firewallName, $user);

        $container->get('session')->set(
            '_security_'.$firewallName,
            serialize($container->get('security.context')->getToken())
        );

        $container->get('session')->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }
}
