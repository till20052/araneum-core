<?php

namespace Araneum\Bundle\MainBundle\Tests\Command;

use Araneum\Bundle\MainBundle\Command\CheckerCheckCommand;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Entity\Application;
use Symfony\Component\Console\Application as App;
use Symfony\Component\Console\Tester\CommandTester;
use \Symfony\Component\DependencyInjection\Container;

/**
 * Class CheckerCheckCommandTest
 *
 * @package Araneum\Bundle\MainBundle\Tests\Command
 */
class CheckerCheckCommandTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CheckerCheckCommand
     */
    private $command;

    /**
     * @var CommandTester
     */
    private $commandTester;

    /**
     * @var
     */
    private $checker;

    /**
     * Test to check Connection in the Command
     */
    public function testCheckConnection()
    {
        $this->checker->expects($this->once())
            ->method('checkConnection')
            ->with($this->equalTo(777))
            ->will($this->returnValue(Connection::STATUS_OK));

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'target' => 'connection',
                'id' => 777,
            ]
        );

        $this->assertEquals(
            1,
            preg_match(
                '/Connection\n ID: (\d+)\n Status: (\w+)/',
                $this->commandTester->getDisplay(),
                $match
            )
        );

        $this->assertEquals(
            Connection::getStatusDescription(Connection::STATUS_OK),
            $match[2]
        );
    }

    /**
     * Test to check Cluster in the Command
     */
    public function testCheckCluster()
    {
        $this->checker->expects($this->once())
            ->method('checkCluster')
            ->with($this->equalTo(777))
            ->will($this->returnValue(Cluster::STATUS_OK));

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'target' => 'cluster',
                'id' => 777,
            ]
        );

        $this->assertEquals(
            1,
            preg_match(
                '/Cluster\n ID: (\d+)\n Status: (\w+)/',
                $this->commandTester->getDisplay(),
                $match
            )
        );

        $this->assertEquals(
            Cluster::getStatusDescription(Cluster::STATUS_OK),
            $match[2]
        );
    }

    /**
     * Test to check Application in the Command
     */
    public function testCheckApplication()
    {
        $this->checker->expects($this->once())
            ->method('checkApplication')
            ->with($this->equalTo(777))
            ->will($this->returnValue(Application::STATUS_OK));

        $this->commandTester->execute(
            [
                'command' => $this->command->getName(),
                'target' => 'application',
                'id' => 777,
            ]
        );

        $this->assertEquals(
            1,
            preg_match(
                '/Application\n ID: (\d+)\n Status: (\w+)/',
                $this->commandTester->getDisplay(),
                $match
            )
        );

        $this->assertEquals(
            Application::getStatusDescription(Application::STATUS_OK),
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

        $this->checker = $this->getMockBuilder('\Araneum\Bundle\MainBundle\Service\ApplicationCheckerService')
            ->disableOriginalConstructor()
            ->getMock();

        $container->expects($this->any())
            ->method('get')
            ->with($this->equalTo('araneum.main.application.checker'))
            ->will($this->returnValue($this->checker));

        return $container;
    }

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $app = new App();
        $app->add(new CheckerCheckCommand());

        /**
         * @var CheckerCheckCommand command
         */
        $this->command = $app->find('checker:check');
        $this->command->setContainer($this->getContainer());

        /**
         * @var CommandTester commandTester
         */
        $this->commandTester = new CommandTester($this->command);
    }
}
