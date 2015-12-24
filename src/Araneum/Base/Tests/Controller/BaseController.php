<?php

namespace Araneum\Base\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Client;
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
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleClientMock;
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $guzzleHttpRequestMock;

    /**
     * Return admin authorized client
     *
     * @param string $authLogin
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected static function createAdminAuthorizedClient($authLogin = 'admin')
    {
        $client = static::createClient();
        $container = $client->getContainer();

        $session = $container->get('session');
        /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
        $userManager = $container->get('fos_user.user_manager');
        /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
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

    /**
     * Creates a Client.
     * If authLogin not empty, client will be create for login user.
     * If firewallName not empty than user will be login by this firewall.
     * If disableReboot is true reboot between requests is off.
     *
     * Mock guzzle with request and set mock guzzle client to container.
     * Mock rabbitmqProducer and set to container.
     *
     * @param string $authLogin
     * @param string|null $firewallName
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     * @param bool|false $disableReboot
     * @return Client
     */
    protected function createClientWithMockServices(
        $authLogin = null,
        $firewallName = null,
        array $options = [],
        array $server = [],
        $disableReboot = false
    ) {
        $client = $this->createClientByParams($options, $server, $authLogin, $firewallName);
        if ($disableReboot) {
            $client->disableReboot();
        }

        $this->mockGuzzleClientWithRequest($client, 'guzzle.client');

        return $client;
    }

    /**
     * Creates a Client by params.
     * If authLogin not empty, client will be create for login user.
     * If firewallName not empty than user will be login by this firewall.
     *
     * @param array $options An array of options to pass to the createKernel class
     * @param array $server  An array of server parameters
     * @param null $authLogin
     * @param null $firewallName
     * @return mixed
     */
    private function createClientByParams(
        array $options = [],
        array $server = [],
        $authLogin = null,
        $firewallName = null
    ) {
        $method = 'createClient';
        $parameters = [$options, $server];
        if (!empty($authLogin)) {
            $method = 'createAdminAuthorizedClient';
            $parameters = [$authLogin, $firewallName];
        }

        return forward_static_call_array([$this, $method], $parameters);
    }

    /**
     * Mock guzzle with request and set mock guzzle client to container.
     *
     * @param Client $client
     * @param string $serviceName
     */
    private function mockGuzzleClientWithRequest(Client $client, $serviceName)
    {
        $this->guzzleClientMock = $this
            ->getMockBuilder('\Guzzle\Service\ClientInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->guzzleHttpRequestMock = $this
            ->getMockBuilder('\Guzzle\Http\Message\Request')
            ->disableOriginalConstructor()
            ->getMock();

        $client->getContainer()->set($serviceName, $this->guzzleClientMock);
    }
}
