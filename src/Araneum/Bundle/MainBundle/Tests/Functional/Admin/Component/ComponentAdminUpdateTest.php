<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Component;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ComponentAdminUpdateTest extends BaseController
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
	 * @var array
	 */
	private static $components = [];

	/**
	 * @var string
	 */
	private static $reservedComponentName = 'Test Reserved Component Name';

	/**
	 * @param $form
	 * @return string
	 */
	private function getPrefix($form)
	{
		return key(array_slice($form->getPhpValues(), 1, 1));
	}

	/**
	 * Return iteration number of dataSource list
	 *
	 * @return int|null
	 */
	private function getDataSetIndex()
	{
		if( ! preg_match("/\"([0-9]+)\"/", $this->getDataSetAsString(false), $match))
			return null;

		return (int) $match[1];
	}

	/**
	 * @return array
	 */
	public static function dataSource()
	{
		return [
			[
				// Simple updating
				'name' => 'Test Component Name',
				'description' => 'Test Component Description',
				'enabled' => true,
				'default' => true,
				'__expected_value' => true
			],
			[
				// Check updating name value if another entity with this value already exists
				'name' => self::$reservedComponentName,
				'description' => null,
				'enabled' => false,
				'default' => false,
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

		foreach([md5(microtime(true)), self::$reservedComponentName] as $name)
		{
			$component = (new Component())
				->setName($name);

			self::$manager->persist($component);
			self::$manager->flush();

			self::$components[] = $component;
		}
	}

	/**
	 * @inheritdoc
	 */
	public static function tearDownAfterClass()
	{
		foreach(self::$components as $component)
		{
			self::$manager->remove(self::$repository->find($component->getId()));
			self::$manager->flush();
		}
	}

	/**
	 * Test update component data
	 *
	 * @param string $name
	 * @param string $description
	 * @param boolean $enabled
	 * @param boolean $default
	 * @param boolean $expectedValue
	 * @dataProvider dataSource
	 * @runInSeparateProcess
	 */
	public function testUpdate($name, $description, $enabled, $default, $expectedValue)
	{
		$client = $this->createAdminAuthorizedClient();

		$component = self::$components[$this->getDataSetIndex()];

		$form = $client
			->request('GET',
				$client
					->getContainer()
					->get('router')
					->generate('admin_araneum_main_component_edit', ['id' => $component->getId()])
			)
			->selectButton('btn_update_and_edit')
			->form();

		$prefix = $this->getPrefix($form);

		$crawler = $client->submit($form, [
			$prefix . '[name]' => $name,
			$prefix . '[description]' => $description,
			$prefix . '[enabled]' => $enabled,
			$prefix . '[default]' => $default
		]);

		$component = self::$repository->find($component->getId());

		$this->assertEquals(count($crawler->filter('.alert-danger')) <= 0, $expectedValue);

		$this->assertEquals($name, $component->getName());
		$this->assertEquals($description, $component->getDescription());
		$this->assertEquals($enabled, $component->isEnabled());
		$this->assertEquals($default, $component->isDefault());
	}
}