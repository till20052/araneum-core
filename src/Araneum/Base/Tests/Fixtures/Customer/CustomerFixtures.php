<?php

namespace Araneum\Base\Tests\Fixtures\Customer;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class CustomerFixtures extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
	const TEST_FIRST_NAME = 'TestCustomerFirstName';
	const TEST_LAST_NAME = 'TestCustomerLastName';
	const TEST_EMAIL = 'TestCustomer@Email.com';
	const TEST_PHONE   = '380998887766';
	const TEST_COUNTRY = 'TestCustomerCountry';
	const TEST_CALLBACK = true;
	const TEST_CURRENCY = 'usd';
	const TEST_2_FIRST_NAME = 'AnotherCustomerFirstName';
	const TEST_2_LAST_NAME  = 'AnotherCustomerLastName';
	const TEST_2_EMAIL = 'TestCustomer2@Email.com';
	const TEST_2_PHONE = '380998887766';
	const TEST_2_COUNTRY    = 'AnotherCustomerCountry';
	const TEST_2_CURRENCY = 'usd';
	const TEST_2_CALLBACK = false;

	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$customer = $manager->getRepository('AraneumAgentBundle:Customer')
			->findByEmail(self::TEST_EMAIL);

		if(empty($customer))
		{
			$manager->persist(
				(new Customer())
					->setApplication($this->getReference('application'))
					->setFirstName(self::TEST_FIRST_NAME)
					->setLastName(self::TEST_LAST_NAME)
					->setCountry(self::TEST_COUNTRY)
					->setEmail(self::TEST_EMAIL)
					->setPhone(self::TEST_PHONE)
					->setCurrency(self::TEST_CURRENCY)
					->setCallback(self::TEST_CALLBACK)
					->setDeliveredAt(new \DateTime('2015-10-07'))
			);
		}

		$customer = $manager->getRepository('AraneumAgentBundle:Customer')
			->findByEmail(self::TEST_2_EMAIL);

		if(empty($customer))
		{
			$manager->persist(
				(new Customer())
					->setApplication($this->getReference('application'))
					->setFirstName(self::TEST_2_FIRST_NAME)
					->setLastName(self::TEST_2_LAST_NAME)
					->setCountry(self::TEST_2_COUNTRY)
					->setEmail(self::TEST_2_EMAIL)
					->setPhone(self::TEST_2_PHONE)
					->setCurrency(self::TEST_2_CURRENCY)
					->setCallback(self::TEST_2_CALLBACK)
					->setDeliveredAt(new \DateTime('2015-10-07'))
			);
		}

		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDependencies()
	{
		return [
			'Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures'
		];
	}
}