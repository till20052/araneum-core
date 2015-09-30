<?php

namespace Araneum\Base\Tests\Fixtures\Main;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\RadBundle\DataFixtures\AbstractFixture;

class UserFixtures extends AbstractFixture implements FixtureInterface
{
    const TEST_USER_NAME = 'TestUserName';
    const TEST_USER_EMAIL = 'TestUserEmail';
    const TEST_USER_FULLNAME = 'TestUserFullName';
    const TEST_USER_PASSWORD = 'TestUserPassword';
    const TEST_USER_ENABLED = true;
    const TEST_USER_ROLES = ['ROLE_USER'];

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername(self::TEST_USER_NAME);
        if (empty($user)) {
            $user = new User();
            $user->setUsername(self::TEST_USER_NAME);
            $user->setEmail(self::TEST_USER_EMAIL);
            $user->setFullName(self::TEST_USER_FULLNAME);
            $user->setPassword(self::TEST_USER_PASSWORD);
            $user->setEnabled(self::TEST_USER_ENABLED);
            $user->setRoles(self::TEST_USER_ROLES);
            $manager->persist($user);
            $manager->flush();

        }
        $this->addReference('owner', $user);
    }
}