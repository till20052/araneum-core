<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Component;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ComponentAdminCreateTest extends BaseController
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
	 * @var string
	 */
	private static $uniqueName = 'Unique Component Name';

	/**
	 * @param $form
	 * @return string
	 */
	private function getPrefix($form)
	{
		return key(array_slice($form->getPhpValues(), 1, 1));
	}

	/**
	 * @return array
	 */
	public static function dataSource()
	{
		return [
			[
				// Simple component creation
				'name' => 'Test Component Name',
				'description' => 'Test Component Description',
				'enabled' => true,
				'default' => true,
				'__expected_value' => true,
			],
			[
				// Check unique component
				'name' => self::$uniqueName,
				'description' => '',
				'enabled' => true,
				'default' => true,
				'__expected_value' => false
			],
			[
				// Too short component name
				'name' => '',
				'description' => '',
				'enabled' => true,
				'default' => true,
				'__expected_value' => false
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

		self::$repository = self::$manager->getRepository('AraneumMainBundle:Component');

		foreach (self::dataSource() as $token) {
			if (is_null($component = self::$repository->findOneByName($token['name'])))
				continue;

			self::$manager->remove($component);
			self::$manager->flush();
		}

		self::$manager->persist((new Component())->setName(self::$uniqueName));
		self::$manager->flush();
	}

	/**
	 * @inheritdoc
	 */
	public static function tearDownAfterClass()
	{
		foreach (self::dataSource() as $token) {
			if (is_null($component = self::$repository->findOneByName($token['name'])))
				continue;

			self::$manager->remove($component);
			self::$manager->flush();
		}
	}

	/**
	 * Test create component
	 *
	 * @param string $name
	 * @param string $description
	 * @param boolean $enabled
	 * @param boolean $default
	 * @param mixed $expectedValue
	 * @dataProvider dataSource
	 * @runInSeparateProcess
	 */
	public function testCreate($name, $description, $enabled, $default, $expectedValue)
	{
		$client = $this->createAdminAuthorizedClient();

		$form = $client
			->request('GET',
				$client
					->getContainer()
					->get('router')
					->generate('admin_araneum_main_component_create')
			)
			->selectButton('btn_create_and_edit')
			->form();
		$prefix = $this->getPrefix($form);

		$form->setValues([
			$prefix . '[name]' => $name,
			$prefix . '[description]' => $description,
			$prefix . '[enabled]' => $enabled,
			$prefix . '[default]' => $default,
		]);

		$this->assertEquals(count($client->submit($form)->filter('.alert-danger')) <= 0, $expectedValue);
	}
}