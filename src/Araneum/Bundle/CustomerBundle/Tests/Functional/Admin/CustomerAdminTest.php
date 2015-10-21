<?php

namespace Araneum\Bundle\CustomerBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Customer\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;

class CustomerAdminTest extends BaseAdminController
{
	const TEST_CUSTOMER_EMAIL = 'TestCustomerEmail@araneum.com';

	protected $createRoute = 'admin_araneum_customer_customer_create';
	protected $updateRoute = 'admin_araneum_customer_customer_edit';
	protected $deleteRoute = 'admin_araneum_customer_customer_delete';
	protected $listRoute = 'admin_araneum_customer_customer_list';

	/**
	 * Set of arguments for testCreate method
	 *
	 * @return array
	 */
	public function createDataSource()
	{
		$client = static::createClient();

		$manager = $client->getContainer()->get('doctrine.orm.entity_manager');

		$application = $manager
			->getRepository('AraneumMainBundle:Application')
			->findOneByName(ApplicationFixtures::TEST_APP_NAME);

		return [
			'Check simple creation' => [
				[
					'application' => $application->getId(),
					'firstName' => 'TestCustomerFirstName',
					'lastName' => 'TestCustomerLastName',
					'country' => 'TestCustomerCountry',
					'email' => self::TEST_CUSTOMER_EMAIL,
					'phone' => '+380995554455',
					'callback' => true,
				],
				true
			],
			'Check customer unique email' => [
				[
					'application' => $application->getId(),
					'email' => CustomerFixtures::TEST_EMAIL
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

		$customer = $manager
			->getRepository('AraneumCustomerBundle:Customer')
			->findOneByEmail(CustomerFixtures::TEST_EMAIL);

		$anotherCustomer = $manager
			->getRepository('AraneumCustomerBundle:Customer')
			->findOneByEmail(CustomerFixtures::TEST_2_EMAIL);

		$application = $manager
			->getRepository('AraneumMainBundle:Application')
			->findOneByName(ApplicationFixtures::TEST_APP_NAME);

		return [
			'Check filter searching application by this application values' => [
				[
					'filter[application][value]' => $application->getId(),
					'filter[firstName][value]' => CustomerFixtures::TEST_FIRST_NAME,
					'filter[lastName][value]' => CustomerFixtures::TEST_LAST_NAME,
					'filter[email][value]' => CustomerFixtures::TEST_EMAIL,
					'filter[phone][value]' => CustomerFixtures::TEST_PHONE,
					'filter[country][value]' => CustomerFixtures::TEST_COUNTRY,
					'filter[createdAt][value][start]' => '01/01/1971',
					'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400),
					'filter[deliveredAt][value][start]' => '01/01/1971',
					'filter[deliveredAt][value][end]' => date('m/d/Y', time() + 86400)
				],
				true,
				$customer
			],
			'Search another application by first filters' => [
				[
					'filter[application][value]' => $application->getId(),
					'filter[firstName][value]' => CustomerFixtures::TEST_FIRST_NAME,
					'filter[lastName][value]' => CustomerFixtures::TEST_LAST_NAME,
					'filter[email][value]' => CustomerFixtures::TEST_EMAIL,
					'filter[phone][value]' => CustomerFixtures::TEST_PHONE,
					'filter[country][value]' => CustomerFixtures::TEST_COUNTRY,
					'filter[createdAt][value][start]' => '01/01/1971',
					'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400),
					'filter[deliveredAt][value][start]' => '01/01/1971',
					'filter[deliveredAt][value][end]' => date('m/d/Y', time() + 86400)
				],
				false,
				$anotherCustomer
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

		$customer = $manager
			->getRepository('AraneumCustomerBundle:Customer')
			->findOneByEmail(CustomerFixtures::TEST_2_EMAIL);

		return [
			'Check simple modification' => [
				[
					'firstName' => 'TestCustomer2UpdateFirstName',
					'lastName' => 'TestCustomer2UpdateLastName',
					'country' => 'TestCustomer2UpdateCountry',
					'email' => self::TEST_CUSTOMER_EMAIL,
					//'phone' => '+380667754444',
					'callback' => ! CustomerFixtures::TEST_2_CALLBACK
				],
				true,
				$customer
			],
			'Check updating email when customer with this email already exists' => [
				[
					'email' => CustomerFixtures::TEST_EMAIL,
				],
				false,
				$customer
			],
			'Set first values of temp application' => [
				[
					'firstName' => CustomerFixtures::TEST_2_FIRST_NAME,
					'lastName' => CustomerFixtures::TEST_2_LAST_NAME,
					'country' => CustomerFixtures::TEST_2_COUNTRY,
					'email' => CustomerFixtures::TEST_2_EMAIL,
					//'phone' => CustomerFixtures::TEST_2_PHONE,
					'callback' => CustomerFixtures::TEST_2_CALLBACK
				],
				true,
				$customer
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
			->getRepository('AraneumCustomerBundle:Customer')
			->findOneByEmail(self::TEST_CUSTOMER_EMAIL);
	}
}