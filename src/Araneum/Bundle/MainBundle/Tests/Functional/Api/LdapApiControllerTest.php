<?php
namespace Araneum\Bundle\MainBundle\Tests\Controller;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Controller\LdapApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Client;

/**
 * LdapApiControllerTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Service;
 */
class LdapApiControllerTest extends BaseController
{
    /**
     * @var LdapApiController
     */
    private $ldap;

    /**
     * Test Ldap Synchronization Controller
     * @runInSeparateProcess
     *
     */
    public function testLdapSynchronization()
    {
        $status = $this->ldap->getLdapSynchronizationAction();
        $this->assertEquals(
            '"Success"',
            $status->getContent()
        );
    }

    /**
     * Mock DI Container
     *
     * @return Container
     */
    private function getContainer()
    {
        $container = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Container')
            ->disableOriginalConstructor()
            ->getMock();

        $this->ldapSync = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Service\LdapSynchronizationService')
            ->disableOriginalConstructor()
            ->getMock();

        $this->ldapSync->expects($this->any())
            ->method('runSynchronization')
            ->will($this->returnValue(true));

        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('api.ldap.synchronization'))
            ->will($this->returnValue($this->ldapSync));

        return $container;
    }

    /**
     * Initialize requirements
     */
    protected function setUp()
    {
        $this->ldap = new LdapApiController();
        $this->ldap->setContainer($this->getContainer());
    }
}
