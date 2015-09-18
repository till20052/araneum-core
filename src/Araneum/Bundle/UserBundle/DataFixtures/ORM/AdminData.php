<?php

namespace Araneum\Bundle\UserBundle\DataFixtures\ORM;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData implements FixtureInterface
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
            $userAdmin->setRoles(['ROLE_ADMIN']);
            $userAdmin->setEnabled(true);
            $manager->persist($userAdmin);
            $manager->flush();
        }
    }
}