<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Component;

use Araneum\Base\Tests\Controller\BaseController;
use Araneum\Bundle\MainBundle\Entity\Component;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DomCrawler\Crawler;

class ComponentAdminFilterTest extends BaseController
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
	private $components = [];

	/**
	 * @var string
	 */
	private $uniqueComponentNameSuffix;

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
	 * @inheritdoc
	 */
	protected function setUp()
	{
		$this->uniqueComponentNameSuffix = ' #'.md5(microtime(true));

		foreach($this->dataSource() as $token)
		{
			$component = (new Component())
				->setName($token['name'].$this->uniqueComponentNameSuffix)
				->setDescription($token['description'])
				->setEnabled($token['enabled'])
				->setDefault($token['default']);

			self::$manager->persist($component);
			self::$manager->flush();

			$this->components[] = $component;
		}
	}

	/**
	 * @BeforeClass
	 */
	public static function setUpBeforeClass()
	{
		self::bootKernel();

		self::$manager = static::$kernel
			->getContainer()
			->get('doctrine.orm.entity_manager');

		self::$repository = self::$manager->getRepository('AraneumMainBundle:Component');
	}

	/**
	 * @return array
	 */
	public function dataSource()
	{
		return [
			[
				'name' => 'Test Component Name',
				'description' => 'Test Component Description',
				'enabled' => false,
				'default' => false,
				'created_at' => [
					'start' => '01/01/1971',
					'end' => date('m/d/Y', time() + 86400)
				],
				'__expected_value' => true
			],
			[
				'name' => 'Test Component Name',
				'description' => 'Test Component Description',
				'enabled' => false,
				'default' => false,
				'created_at' => [
					'start' => '01/01/1970',
					'end' => '01/01/1970'
				],

				'__expected_value' => false
			]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function tearDown()
	{
		foreach($this->components as $component)
		{
			self::$manager->remove(self::$repository->find($component->getId()));
			self::$manager->flush();
		}
	}

	/**
	 * Test searching mechanism
	 *
	 * @param string $name
	 * @param string $description
	 * @param boolean $enabled
	 * @param boolean $default
	 * @param array $createdAt
	 * @param array $expectedValue
	 * @dataProvider dataSource
	 * @runInSeparateProcess
	 */
	public function testFilter($name, $description, $enabled, $default, $createdAt, $expectedValue)
	{
		$client = $this->createAdminAuthorizedClient();

		$form = $client
			->request('GET',
				$client
					->getContainer()
					->get('router')
					->generate('admin_araneum_main_component_list')
			)
			->selectButton('Filter')
			->form([
				'filter[name][value]' => $name,
				'filter[description][value]' => $description,
				'filter[enabled][value]' => (int) $enabled,
				'filter[default][value]' => (int) $default,
				'filter[createdAt][value][start]' => $createdAt['start'],
				'filter[createdAt][value][end]' => $createdAt['end']
			], 'GET');

		$crawler = $client->submit($form);

		$list = $crawler->filter('table.table > tbody > tr > td:nth-child(2) > a')->each(function(Crawler $node){
			return (int) $node->text();
		});

		$this->assertEquals(in_array($this->components[$this->getDataSetIndex()]->getId(), $list), $expectedValue);
	}
}