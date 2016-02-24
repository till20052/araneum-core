<?php

namespace Araneum\Bundle\MainBundle\Tests\Unit\Command;

use Araneum\Bundle\MainBundle\Command\LdapCommand;
use Araneum\Bundle\UserBundle\Entity\User;
use Symfony\Component\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;
use \Symfony\Component\DependencyInjection\Container;

/**
 * Class LdapCommandTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Unit\Command
 */
class LdapCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LdapCommand
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var LdapSynchronizationService
     */
    private $ldapSync;

    /**
     * Test to Synchronization LDAP in command
     */
    public function testLdapSynchronization()
    {
        $this->ldapSync->expects($this->once())
            ->method('runSynchronization')
            ->will($this->returnValue(['sitem' => 1, 'uitem' => 1]));

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
            ]
        );

        $this->assertEquals(
            1,
            preg_match(
                '/User: Save\((\d)\)\/Update\((\d)\).\nSuccess\n/',
                $this->commandTester->getDisplay(),
                $match
            )
        );

        $this->assertEquals(
            1,
            $match[1]
        );

        $this->assertEquals(
            1,
            $match[2]
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

        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('api.ldap.synchronization'))
            ->will($this->returnValue($this->ldapSync));

        return $container;
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $app = new App();
        $app->add(new LdapCommand());

        /**
         * @var LdapCommand command
         */
        $this->command = $app->find('araneum:ldap:users');
        $this->command->setContainer($this->getContainer());

        /**
         * @var CommandTester commandTester
         */
        $this->commandTester = new CommandTester($this->command);
    }
}
