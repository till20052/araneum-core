<?php

namespace Araneum\Bundle\UserBundle\Tests\Functional\Utils\Data;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserManager
{
    /**
     * Delete User by array $criteria
     *
     * @param EntityManager $manager
     * @param array         $criteria
     * @return bool
     */
    public static function delete(EntityManager $manager, array $criteria)
    {
        $user = $manager->getRepository('AraneumUserBundle:User')->findOneBy($criteria);

        if (!empty($user)) {
            $manager->remove($user);
            $manager->flush($user);

            return true;
        }

        return false;
    }

    /**
     * Create user if not exist by criteria
     *
     * @param EntityManager $manager
     * @param array         $criteria
     * @return User
     */
    public static function create(EntityManager $manager, array $criteria = [])
    {
        $criteria = array_merge(['username' => 'testAdminUser'], $criteria);
        $user = $manager->getRepository('AraneumUserBundle:User')->findOneBy($criteria);
        if (empty($user)) {
            $user = new User();
            $user->setUsername($criteria['username']);
            $user->setEmail(array_key_exists('email', $criteria) ? $criteria['email'] : 'testAdminUser@test.test');
            $user->setFullName(array_key_exists('fullName', $criteria) ? $criteria['fullName'] : 'testAdminUser');
            $user->setEnabled(array_key_exists('enabled', $criteria) ? $criteria['enabled'] : false);
            $user->setCreatedAt(array_key_exists('createdAt', $criteria) ? $criteria['createdAt'] : new \DateTime());
            $user->addRole('ROLE_USER');
            $user->setPlainPassword('123');

            $manager->persist($user);
            $manager->flush($user);
        }

        return $user;
    }
}