<?php

namespace Araneum\Base\Tests\Fixtures\Customer;

use Araneum\Bundle\AgentBundle\Entity\Customer;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Class CustomerFixtures
 *
 * @package Araneum\Base\Tests\Fixtures\Customer
 */
class CustomerFixtures extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    const TEST_FIRST_NAME   = 'TestCustomerFirstName';
    const TEST_LAST_NAME    = 'TestCustomerLastName';
    const TEST_EMAIL        = 'TestCustomer@Email.com';
    const TEST_PHONE        = '380998887766';
    const TEST_COUNTRY      = '1';
    const TEST_CALLBACK     = true;
    const TEST_CURRENCY     = 'usd';
    const TEST_PASSWORD     = 'testPassword';
    const TEST_BIRTHDAY     = '1980-23-11';
    const TEST_2_FIRST_NAME = 'AnotherCustomerFirstName';
    const TEST_2_LAST_NAME  = 'AnotherCustomerLastName';
    const TEST_2_EMAIL      = 'TestCustomer2@Email.com';
    const TEST_2_PHONE      = '380998887766';
    const TEST_2_COUNTRY    = '22';
    const TEST_2_CURRENCY   = 'usd';
    const TEST_2_CALLBACK   = false;
    const TEST_2_PASSWORD   = 'testPassword2';
    const TEST_2_BIRTHDAY   = '2015-22-10';


    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $customer = $manager->getRepository('AraneumAgentBundle:Customer')
            ->findByEmail(self::TEST_EMAIL);

        if (empty($customer)) {
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
                    ->setBirthday(new \DateTime(self::TEST_BIRTHDAY))
                    ->setDeliveredAt(new \DateTime('2015-10-07'))
            );
        }

        $customer = $manager->getRepository('AraneumAgentBundle:Customer')
            ->findByEmail(self::TEST_2_EMAIL);

        if (empty($customer)) {
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
                    ->setBirthday(new \DateTime(self::TEST_2_BIRTHDAY))
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
            'Araneum\Base\Tests\Fixtures\Main\ApplicationFixtures',
        ];
    }
}
