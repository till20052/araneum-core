<?php

namespace Araneum\Bundle\UserBundle\DataFixtures\ORM;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AdminData implements FixtureInterface
{
    /**
     * {@inheritDoc}
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

        $userApi = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('apiuser');
        if (empty($userApi)) {
            $userApi = new User();
            $userApi->setUsername('apiuser');
            $userApi->setPlainPassword('apiuser_321');
            $userApi->setEmail('apiuser@araneum.dev');
            $userApi->setRoles([User::ROLE_API]);
            $userApi->setEnabled(true);
            $manager->persist($userApi);
        }
        $manager->flush();
    }
}