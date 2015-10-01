<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Component;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ComponentAdminSetEnableStateTest extends BaseController
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
	 * @var Component
	 */
	private static $component;

	/**
	 * @inheritdoc
	 */
	public static function setUpBeforeClass()
	{
		self::bootKernel();

		self::$manager = static::$kernel->getContainer()
			->get('doctrine.orm.entity_manager');

		self::$repository = self::$manager->getRepository('AraneumMainBundle:Component');

		self::$component = new Component();
		self::$component->setName('TestComponent#' . md5(microtime(true)));
		self::$component->setEnabled(false);

		self::$manager->persist(self::$component);
		self::$manager->flush();
	}

	/**
	 * @inheritdoc
	 */
	public static function tearDownAfterClass()
	{
		self::$manager->remove(self::$component);
		self::$manager->flush();
	}

	/**
	 * Test set component enable state
	 *
	 * @runInSeparateProcess
	 */
	public function testSetEnableState()
	{
		$client = $this->createAdminAuthorizedClient();

		$client->request('POST',
			$client->getContainer()
				->get('router')
				->generate('sonata_admin_set_object_field_value')
			. '?'
			. http_build_query([
				'context' => 'list',
				'field' => 'enabled',
				'objectId' => self::$component->getId(),
				'code' => 'araneum.main.admin.component'
			]), [
				'name' => '',
				'value' => true,
				'pk' => self::$component->getId()
			], [], [
				'HTTP_X-Requested-With' => 'XMLHttpRequest'
			]);

		self::$component = self::$repository->find(self::$component->getId());

		$this->assertTrue(self::$component->isEnabled());
	}
}