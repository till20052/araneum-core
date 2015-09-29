<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Component;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ComponentAdminDeleteTest extends BaseController
{
	/**
	 * @var EntityManager
	 */
	private $manager;

	/**
	 * @var EntityRepository
	 */
	private $repository;

	/**
	 * @var Component
	 */
	private $component;

	/**
	 * @inheritdoc
	 */
	public function setUp()
	{
		self::bootKernel();

		$this->manager = static::$kernel->getContainer()
			->get('doctrine.orm.entity_manager');

		$this->repository = $this->manager->getRepository('AraneumMainBundle:Component');

		$this->component = (new Component())
			->setName(md5(microtime(true)));

		$this->manager->persist($this->component);
		$this->manager->flush();
	}

	/**
	 * Test delete component
	 *
	 * @runInSeparateProcess
	 */
	public function testDelete()
	{
		$client = $this->createAdminAuthorizedClient();

		$router = $client->getContainer()->get('router');

		$form = $client
			->request('GET', $router->generate('admin_araneum_main_component_delete', [
				'id' => $this->component->getId()
			]))
			->selectButton('Yes, delete')
			->form();

		$client->submit($form);

		$this->assertTrue(
			$client
				->getResponse()
				->isRedirect($router->generate('admin_araneum_main_component_list'))
		);
		$this->assertNull($this->repository->find($this->component->getId()));
	}
}