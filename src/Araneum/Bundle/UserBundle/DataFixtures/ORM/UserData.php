<?php

namespace Araneum\Bundle\UserBundle\DataFixtures\ORM;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

class UserData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
	const API_USER   = 'api';
	const API_PASSWD = 'apiApp_user123';

	/**
	 * Load data fixtures with the passed EntityManager
	 *
	 * @param ObjectManager $manager
	 */
	public function load(ObjectManager $manager)
	{
		$userAdmin = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('admin');
		if (empty($userAdmin)) {
			$userAdmin = new User();
			$userAdmin->setUsername('admin');
			$userAdmin->setPlainPassword('admin_123');
			$userAdmin->setEmail('admin@araneum.dev');
			$userAdmin->setRoles([User::ROLE_ADMIN]);
			$userAdmin->setEnabled(true);
			$manager->persist($userAdmin);
		}

		$userApi = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('api');
		if (empty($userApi)) {
			$userApi = new User();
			$userApi->setUsername(self::API_USER);
			$userApi->setPlainPassword(self::API_PASSWD);
			$userApi->setEmail('apiuser@araneum.dev');
			$userApi->setRoles([User::ROLE_API]);
			$userApi->setEnabled(true);
			$manager->persist($userApi);
		}

		$manager->flush();
	}

	/**
	 * @inheritdoc
	 */
	public function getDependencies()
	{
		return [
			'Araneum\Bundle\UserBundle\DataFixtures\ORM\RoleData'
		];
	}
}