<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\ComponentFixtures;

class ComponentAdminTest extends BaseAdminController
{
	protected $createRoute = 'admin_araneum_main_component_create';
	protected $updateRoute = 'admin_araneum_main_component_edit';
	protected $deleteRoute = 'admin_araneum_main_component_delete';
	protected $listRoute   = 'admin_araneum_main_component_list';

	const COMPONENT_TEST_NAME = 'TestComponentTempName';

	/**
	 * Set of arguments for testCreate method
	 *
	 * @return array
	 */
	public function createDataSource()
	{
		return [
			[
				[
					// Simple component creation
					'name' => self::COMPONENT_TEST_NAME,
					'description' => '',
					'enabled' => true,
					'default' => true,
				],
				true
			],
			[
				[
					// Check component unique name
					'name' => self::COMPONENT_TEST_NAME,
					'description' => '',
					'enabled' => true,
					'default' => true,
				],
				false
			]
		];
	}

	/**
	 * Set of arguments for testFilter method
	 *
	 * @return array
	 */
	public function filterDataSource()
	{
		$client = static::createClient();

		$manager = $client->getContainer()->get('doctrine.orm.entity_manager');

		$fxtComponent = $manager->getRepository('AraneumMainBundle:Component')
			->findOneByName(ComponentFixtures::TEST_COMP_NAME);

		$tmpComponent = $manager->getRepository('AraneumMainBundle:Component')
			->findOneByName(self::COMPONENT_TEST_NAME);

		return  [
			[
				[
					'filter[name][value]' => ComponentFixtures::TEST_COMP_NAME,
					'filter[description][value]' => ComponentFixtures::TEST_COMP_DESC,
					'filter[enabled][value]' => ComponentFixtures::TEST_COMP_ENABLED,
					'filter[default][value]' => ComponentFixtures::TEST_COMP_DEFAULT,
					'filter[createdAt][value][start]' => '01/01/1971',
					'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
				],
				true,
				$fxtComponent
			],
			[
				[
					'filter[name][value]' => ComponentFixtures::TEST_COMP_NAME,
					'filter[description][value]' => ComponentFixtures::TEST_COMP_DESC,
					'filter[enabled][value]' => ComponentFixtures::TEST_COMP_ENABLED,
					'filter[default][value]' => ComponentFixtures::TEST_COMP_DEFAULT,
					'filter[createdAt][value][start]' => '01/01/1971',
					'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
				],
				false,
				$tmpComponent
			]
		];
	}

	/**
	 * Set of arguments for testUpdate method
	 *
	 * @return array
	 */
	public function updateDataSource()
	{
		$client = static::createClient();

		$manager = $client->getContainer()->get('doctrine.orm.entity_manager');

		$tmpComponent = $manager->getRepository('AraneumMainBundle:Component')
			->findOneByName(self::COMPONENT_TEST_NAME);

		return [
			[
				[
					// Check simple modification
					'name' => md5(microtime(true)),
					'description' => '',
					'enabled' => true,
					'default' => true,
				],
				true,
				$tmpComponent
			],
			[
				[
					// Check component unique name
					'name' => ComponentFixtures::TEST_COMP_NAME,
					'description' => '',
					'enabled' => true,
					'default' => true,
				],
				false,
				$tmpComponent
			],
			[
				[
					// return tmp component name
					'name' => self::COMPONENT_TEST_NAME,
					'description' => '',
					'enabled' => true,
					'default' => true,
				],
				true,
				$tmpComponent
			]
		];
	}

	/**
	 * Return entity for testDelete method
	 *
	 * @return mixed
	 */
	public function deleteDataSource()
	{
		$client = static::createClient();

		return $client
			->getContainer()
			->get('doctrine.orm.entity_manager')
			->getRepository('AraneumMainBundle:Component')
			->findOneByName(self::COMPONENT_TEST_NAME);
	}
}