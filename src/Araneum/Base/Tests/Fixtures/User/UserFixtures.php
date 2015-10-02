<?php

namespace Araneum\Base\Tests\Fixtures\User;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\RadBundle\DataFixtures\AbstractFixture;

class UserFixtures extends AbstractFixture implements FixtureInterface
{
    const TEST_USER_NAME     = 'TestUserName';
    const TEST_USER_EMAIL    = 'test@user.email';
    const TEST_USER_FULLNAME = 'TestUserFullName';
    const TEST_USER_PASSWORD = 'TestUserPassword';
    const TEST_USER_ENABLED  = true;
    const TEST_USER_ROLES    = ['ROLE_USER'];

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
        }
        $emailUser = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('emailuser');

        if (empty($emailUser)) {
            $emailUser = new User();
            $emailUser->setUsername('emailuser');
            $emailUser->setPlainPassword('emailuser_123');
            $emailUser->setEmail('emailuser@araneum.dev');
            $emailUser->setRoles(['ROLE_ADMIN']);
            $emailUser->setEnabled(true);
        }
        $emptyUser = $manager->getRepository('AraneumUserBundle:User')->findOneByUsername('emptyuser');

        if (empty($emptyUser)) {
            $emptyUser = new User();
            $emptyUser->setUsername('emptyuser');
            $emptyUser->setPlainPassword('emptyuser_123');
            $emptyUser->setEmail('emptyuser@araneum.dev');
            $emptyUser->setRoles(['ROLE_ADMIN']);
            $emptyUser->setEnabled(true);
        }
        $manager->persist($user);
        $manager->persist($emailUser);
        $manager->persist($emptyUser);
        $manager->flush();
        $this->addReference('owner', $user);
    }
}