<?php

namespace Araneum\Bundle\UserBundle\DataFixtures\ORM;

use Araneum\Bundle\UserBundle\Entity\Role;
use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class RoleData
 *
 * @package Araneum\Bundle\UserBundle\DataFixtures\ORM
 */
class RoleData extends AbstractFixture implements FixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roles = [];
        $repository = $manager->getRepository('AraneumUserBundle:Role');

        foreach (User::$roleNames as $roleName) {
            $roles[$roleName] = $repository->findOneByName($roleName);

            if (!empty($roles[$roleName])) {
                continue;
            }

            $roles[$roleName] = (new Role())->setName($roleName);

            $manager->persist($roles[$roleName]);
        }

        $manager->flush();
    }
}
