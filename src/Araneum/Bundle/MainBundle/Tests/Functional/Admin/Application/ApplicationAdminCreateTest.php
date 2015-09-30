<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin\Application;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Base\Tests\Fixtures\Main\ClusterFixtures;
use Araneum\Base\Tests\Fixtures\Main\ComponentFixtures;
use Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures;
use Araneum\Base\Tests\Fixtures\Main\LocaleFixtures;
use Araneum\Base\Tests\Fixtures\Main\UserFixtures;

class ApplicationAdminTest extends BaseAdminController
{
	protected $createRoute = 'admin_araneum_main_application_create';
	protected $updateRoute = 'admin_araneum_main_application_edit';
	protected $deleteRoute = 'admin_araneum_main_application_delete';
	protected $listRoute   = 'admin_araneum_main_application_list';

	const APP_TEST_NAME = 'TestApplicationTempName';

	/**
	 * Set of arguments for testCreate method
	 *
	 * @return array
	 */
	public function createDataSource()
	{
		$client = static::createClient();

		$manager = $client->getContainer()->get('doctrine.orm.entity_manager');

		$cluster = $manager->getRepository('AraneumMainBundle:Cluster')
			->findOneByName(ClusterFixtures::TEST_CLU_NAME);

		$db = $manager->getRepository('AraneumMainBundle:Connection')
			->findOneByName(ConnectionFixtures::TEST_CONN_DB_NAME);

		$locale = $manager->getRepository('AraneumMainBundle:Locale')
			->findOneByName(LocaleFixtures::TEST_LOC_NAME);

		$components = $manager->getRepository('AraneumMainBundle:Component')
			->findOneByName(ComponentFixtures::TEST_COMP_NAME);

		return [
			[
				[
					// Check simple creation
					'name' => self::APP_TEST_NAME,
					'domain' => 'domain.com',
					'aliases' => 'www.domain.com, ww2.domain.com',
					'public' => true,
					'enabled' => true,
					'template' => 'template.html',
					'cluster' => $cluster->getId(),
					'db' => $db->getId(),
					'locale' => $locale->getId(),
					'components' => [$components->getId()],
				],
				true
			],
			[
				[
					// Check application unique name
					'name' => self::APP_TEST_NAME,
					'domain' => 'domain.com',
					'aliases' => 'www.domain.com, ww2.domain.com',
					'public' => true,
					'enabled' => true,
					'template' => 'template.html',
					'cluster' => $cluster->getId(),
					'db' => $db->getId(),
					'locale' => $locale->getId(),
					'components' => [$components->getId()],
				],
				false
			],
			[
				[
					// Check domain validator
					'name' => md5(microtime(true)),
					'domain' => 'domain',
					'public' => true,
					'enabled' => true,
					'template' => 'template.html',
					'cluster' => $cluster->getId(),
					'db' => $db->getId(),
					'locale' => $locale->getId(),
					'components' => [$components->getId()],
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

		$cluster = $manager->getRepository('AraneumMainBundle:Cluster')
			->findOneByName(ClusterFixtures::TEST_CLU_NAME);

		$db = $manager->getRepository('AraneumMainBundle:Connection')
			->findOneByName(ConnectionFixtures::TEST_CONN_DB_NAME);

		$locale = $manager->getRepository('AraneumMainBundle:Locale')
			->findOneByName(LocaleFixtures::TEST_LOC_NAME);

		$owner = $manager->getRepository('AraneumUserBundle:User')
			->findOneByEmail(UserFixtures::TEST_USER_EMAIL);

		$fxtApplication = $manager->getRepository('AraneumMainBundle:Application')
			->findOneByName(ApplicationFixtures::TEST_APP_NAME);

		$tmpApplication = $manager->getRepository('AraneumMainBundle:Application')
			->findOneByName(self::APP_TEST_NAME);

		return [
			[
				[
					'filter[cluster][value]' => $cluster->getId(),
					'filter[name][value]' => ApplicationFixtures::TEST_APP_NAME,
					'filter[domain][value]' => ApplicationFixtures::TEST_APP_DOMAIN,
					'filter[db][value]' => $db->getId(),
					'filter[public][value]' => ApplicationFixtures::TEST_APP_PUBLIC,
					'filter[enabled][value]' => ApplicationFixtures::TEST_APP_ENABLED,
					'filter[locale][value]' => $locale->getId(),
					'filter[owner][value]' => $owner->getId(),
					'filter[status][value]' => ApplicationFixtures::TEST_APP_STATUS,
					'filter[template][value]' => ApplicationFixtures::TEST_APP_TEMPLATE,
					'filter[createdAt][value][start]' => '01/01/1971',
					'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
				],
				true,
				$fxtApplication
			],
			[
				[
					'filter[cluster][value]' => $cluster->getId(),
					'filter[name][value]' => ApplicationFixtures::TEST_APP_NAME,
					'filter[domain][value]' => ApplicationFixtures::TEST_APP_DOMAIN,
					'filter[db][value]' => $db->getId(),
					'filter[public][value]' => ApplicationFixtures::TEST_APP_PUBLIC,
					'filter[enabled][value]' => ApplicationFixtures::TEST_APP_ENABLED,
					'filter[locale][value]' => $locale->getId(),
					'filter[owner][value]' => $owner->getId(),
					'filter[status][value]' => ApplicationFixtures::TEST_APP_STATUS,
					'filter[template][value]' => ApplicationFixtures::TEST_APP_TEMPLATE,
					'filter[createdAt][value][start]' => '01/01/1971',
					'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
				],
				false,
				$tmpApplication
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

		$tmpApplication = $manager->getRepository('AraneumMainBundle:Application')
			->findOneByName(self::APP_TEST_NAME);

		return [
			[
				[
					// Check simple modification
					'name' => md5(microtime(true)),
					'domain' => 'domain.com',
					'aliases' => 'www.domain.com, ww2.domain.com',
					'public' => true,
					'enabled' => true,
					'template' => 'template.html'
				],
				true,
				$tmpApplication
			],
			[
				[
					// Check updating name if application with this name exists
					'name' => ApplicationFixtures::TEST_APP_NAME,
					'domain' => 'domain.com',
					'aliases' => 'www.domain.com, ww2.domain.com',
					'public' => true,
					'enabled' => true,
					'template' => 'template.html'
				],
				false,
				$tmpApplication
			],
			[
				[
					// return tmp application name
					'name' => self::APP_TEST_NAME,
					'domain' => 'domain.com',
					'aliases' => 'www.domain.com, ww2.domain.com',
					'public' => false,
					'enabled' => false,
					'template' => 'template.html'
				],
				true,
				$tmpApplication
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
			->getRepository('AraneumMainBundle:Application')
			->findOneByName(self::APP_TEST_NAME);
	}
}