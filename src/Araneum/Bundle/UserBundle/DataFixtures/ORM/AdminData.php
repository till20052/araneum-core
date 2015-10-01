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
            $userAdmin->setRoles(['ROLE_ADMIN']);
            $userAdmin->setEnabled(true);
            $manager->persist($userAdmin);
            $manager->flush();
        }
        $emailUser = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('emailuser');
        if (empty($emailUser)) {
            $emailUser = new User();
            $emailUser->setUsername('emailuser');
            $emailUser->setPlainPassword('emailuser_123');
            $emailUser->setEmail('emailuser@araneum.dev');
            $emailUser->setRoles(['ROLE_ADMIN']);
            $emailUser->setEnabled(true);
            $manager->persist($emailUser);
            $manager->flush();
        }
        $emptyUser = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('emptyuser');
        if (empty($emptyUser)) {
            $emptyUser = new User();
            $emptyUser->setUsername('emptyuser');
            $emptyUser->setPlainPassword('emptyuser_123');
            $emptyUser->setEmail('emptyuser@araneum.dev');
            $emptyUser->setRoles(['ROLE_ADMIN']);
            $emptyUser->setEnabled(true);
            $manager->persist($emptyUser);
            $manager->flush();
        }
    }
}