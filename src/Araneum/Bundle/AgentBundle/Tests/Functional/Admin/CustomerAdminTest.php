<?php

namespace Araneum\Bundle\AgentBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Customer\CustomerFixtures;
use Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures;
use Araneum\Base\Tests\Controller\BaseController;
use Symfony\Component\DomCrawler\Crawler;

class CustomerAdminTest extends BaseController
{
	const TEST_CUSTOMER_EMAIL = 'TestCustomerEmail';

	/**
	 * Test is create action is disabled
	 *
	 *
	 */
	public function testDisableCreate()
	{
		$client = $this->createAdminAuthorizedClient();

		$crawler = $client->request(
			'GET',
			'/en/admin/araneum/agent/customer/create'
		);

		$this->assertFalse($client->getResponse()->isSuccessful());
	}

	/**
	 * Test is edit action is disabled
	 *
	 *
	 */
	public function testDisableEdit()
	{
		$client = $this->createAdminAuthorizedClient();

		$customer = $client->getContainer()
			->get('doctrine.orm.entity_manager')
			->getRepository('AraneumAgentBundle:Customer')
			->findOneByEmail(CustomerFixtures::TEST_EMAIL);

		$crawler = $client->request(
			'GET',
			'/en/admin/araneum/agent/customer/' . $customer->getId() . '/edit'
		);

		$this->assertFalse($client->getResponse()->isSuccessful());
	}

	/**
	 * Test is delete action is disabled
	 *
	 *
	 */
	public function testDisableDelete()
	{
		$client = $this->createAdminAuthorizedClient();

		$customer = $client->getContainer()
			->get('doctrine.orm.entity_manager')
			->getRepository('AraneumAgentBundle:Customer')
			->findOneByEmail(CustomerFixtures::TEST_EMAIL);

		$crawler = $client->request(
			'GET',
			'/en/admin/araneum/agent/customer/' . $customer->getId() . '/delete'
		);

		$this->assertFalse($client->getResponse()->isSuccessful());
	}


	/**
	 * Nest of filter
	 *
	 *
	 */
	public function testFilter()
	{
		$client = $this->createAdminAuthorizedClient();
		$crawler = $client->request(
			'GET',
			$client->getContainer()->get('router')->generate('admin_araneum_agent_customer_list', ['_locale' => 'en'])
		);

		$manager = $client->getContainer()->get('doctrine.orm.entity_manager');

		$customer = $manager
			->getRepository('AraneumAgentBundle:Customer')
			->findOneByEmail(CustomerFixtures::TEST_EMAIL);

		$application = $manager
			->getRepository('AraneumMainBundle:Application')
			->findOneByName(ApplicationFixtures::TEST_APP_NAME);

		$fullFormInput = [
			'filter[application][value]' => $application->getId(),
			'filter[firstName][value]' => CustomerFixtures::TEST_FIRST_NAME,
			'filter[lastName][value]' => CustomerFixtures::TEST_LAST_NAME,
			'filter[email][value]' => CustomerFixtures::TEST_EMAIL,
			'filter[phone][value]' => CustomerFixtures::TEST_PHONE,
			'filter[country][value]' => CustomerFixtures::TEST_COUNTRY
		];

		$form = $crawler->selectButton('Filter')->form($fullFormInput);
		$crawler = $client->submit($form);

		$list = $crawler->filter('table.table > tbody > tr > td')
			->each(
				function (Crawler $node) {
					return (int)$node->text();
				}
			);

		$this->assertEquals(true, in_array($customer->getId(), $list));
	}
}