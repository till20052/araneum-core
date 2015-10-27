<?php

namespace Araneum\Bundle\MainBundle\Tests\Command;

use Araneum\Bundle\MainBundle\Command\CheckerCheckCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use \Symfony\Component\DependencyInjection\Container;

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
	 * Mock Get Output method in Checker Service
	 *
	 * @param array $structure
	 * @param null $invokedCount
	 */
	private function mockGetOutputMethod($structure, $invokedCount = null)
	{
		$output = new \stdClass();

		foreach ($structure as $key => $val) {
			$output->{$key} = $val;
		}

		$this->checker->expects(is_null($invokedCount) ? $this->once() : $invokedCount)
			->method('getOutput')
			->will($this->returnValue($output));
	}

	/**
	 * @inheritdoc
	 */
	protected function setUp()
	{
		$app = new Application();
		$app->add(new CheckerCheckCommand());

		/** @var CheckerCheckCommand command */
		$this->command = $app->find('checker:check');
		$this->command->setContainer($this->getContainer());

		/** @var CommandTester commandTester */
		$this->commandTester = new CommandTester($this->command);
	}

	/**
	 * Test to check Connection in the Command
	 */
	public function testCheckConnection()
	{
		$this->checker->expects($this->once())
			->method('checkConnection')
			->with($this->equalTo(777))
			->will($this->returnValue(true));

		$this->mockGetOutputMethod(
			[
				'packetsTransmitted' => 1,
				'received' => 2,
				'packetLoss' => 3,
				'time' => 4
			]
		);

		$this->commandTester->execute([
			'command' => $this->command->getName(),
			'target' => 'connection',
			'id' => 777
		]);

		$this->assertEquals(5, preg_match_all(
			'/\:\s(\d+)/',
			$this->commandTester->getDisplay(),
			$match
		));

		$this->assertTrue($match[1] === ['1', '2', '3', '4', '1']);
	}

	/**
	 * Test to check Cluster in the Command
	 */
	public function testCheckCluster()
	{
		$this->checker->expects($this->once())
			->method('checkCluster')
			->with($this->equalTo(777))
			->will($this->returnValue(true));

		$this->mockGetOutputMethod(['statusDescription' => 1]);

		$this->commandTester->execute([
			'command' => $this->command->getName(),
			'target' => 'cluster',
			'id' => 777
		]);

		$this->assertEquals(1, preg_match_all(
			'/\:\s(\d+)/',
			$this->commandTester->getDisplay(),
			$match
		));

		$this->assertTrue($match[1] === ['1']);
	}

	/**
	 * Test to check Application in the Command
	 */
	public function testCheckApplication()
	{
		$this->checker->expects($this->once())
			->method('checkApplication')
			->with($this->equalTo(777))
			->will($this->returnValue(true));

		$this->commandTester->execute([
			'command' => $this->command->getName(),
			'target' => 'application',
			'id' => 777
		]);

		$this->assertEquals(1, preg_match_all(
			'/\:\s(\d+)/',
			$this->commandTester->getDisplay(),
			$match
		));

		$this->assertTrue($match[1] === ['1']);
	}
}