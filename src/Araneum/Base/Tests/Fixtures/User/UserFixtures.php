<?php

namespace Araneum\Base\Tests\Fixtures\User;

use Araneum\Bundle\UserBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\RadBundle\DataFixtures\AbstractFixture;

class UserFixtures extends AbstractFixture implements FixtureInterface
{
    const TEST_USER_NAME            = 'TestUserName';
    const TEST_USER_EMAIL           = 'TestUserEmail@test.com';
    const TEST_USER_FULLNAME        = 'TestUserFullName';
    const TEST_USER_PASSWORD        = 'TestUserPassword';
    const TEST_USER_ENABLED         = true;
    const TEST_USER_ROLES           = ['ROLE_USER'];
    const TEST_USER_NAME_DELETE     = 'TestUserNameForDelete';
    const TEST_USER_EMAIL_FILTER    = 'TestUserEmailForFilter@test.com';
    const TEST_USER_FULLNAME_FILTER = 'TestUserFullnameForFilter';
    const TEST_USER_ENABLED_FILTER  = true;
    const TEST_USER_NAME_UPDATE     = 'TestUserNameForUpdate';
    const TEST_USER_EMAIL_UPDATE    = 'TestUserEmailForUpdate@test.com';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = $manager->getRepository('AraneumUserBundle:User')
            ->findOneByUsername(self::TEST_USER_NAME);
        if (empty($user)) {
            $user = new User();
            $user->setUsername(self::TEST_USER_NAME);
            $user->setEmail(self::TEST_USER_EMAIL);
            $user->setFullName(self::TEST_USER_FULLNAME);
            $user->setPlainPassword(self::TEST_USER_PASSWORD);
            $user->setEnabled(self::TEST_USER_ENABLED);
            $user->setRoles(self::TEST_USER_ROLES);
            $manager->persist($user);
        }
        $this->addReference('owner', $user);

        $userForDelete = $manager->getRepository('AraneumUserBundle:User')
            ->findOneByUsername(self::TEST_USER_NAME_DELETE);
        if (empty($userForDelete)) {
            $userForDelete = new User();
            $userForDelete->setUsername(self::TEST_USER_NAME_DELETE);
            $userForDelete->setEmail('TestUserNameForDelete@test.com');
            $userForDelete->setFullName(self::TEST_USER_FULLNAME);
            $userForDelete->setPlainPassword(self::TEST_USER_PASSWORD);
            $userForDelete->setEnabled(self::TEST_USER_ENABLED);
            $userForDelete->setRoles(self::TEST_USER_ROLES);
            $manager->persist($userForDelete);
        }

        $userForFilter = $manager->getRepository('AraneumUserBundle:User')
            ->findOneByEmail(self::TEST_USER_EMAIL_FILTER);
        if (empty($userForFilter)) {
            $userForFilter = new User();
            $userForFilter->setUsername('TestUserEmailForFilter');
            $userForFilter->setEmail(self::TEST_USER_EMAIL_FILTER);
            $userForFilter->setFullName(self::TEST_USER_FULLNAME_FILTER);
            $userForFilter->setPlainPassword(self::TEST_USER_PASSWORD);
            $userForFilter->setEnabled(self::TEST_USER_ENABLED_FILTER);
            $userForFilter->setRoles(self::TEST_USER_ROLES);
            $userForFilter->setCreatedAt(new \DateTime('1980-11-25'));
            $manager->persist($userForFilter);
        }

        $userForUpdate = $manager->getRepository('AraneumUserBundle:User')
            ->findOneByEmail(self::TEST_USER_EMAIL_UPDATE);
        if (empty($userForUpdate)) {
            $userForUpdate = new User();
            $userForUpdate->setUsername(self::TEST_USER_NAME_UPDATE);
            $userForUpdate->setEmail(self::TEST_USER_EMAIL_UPDATE);
            $userForUpdate->setFullName(self::TEST_USER_FULLNAME);
            $userForUpdate->setPlainPassword(self::TEST_USER_PASSWORD);
            $userForUpdate->setEnabled(self::TEST_USER_ENABLED);
            $userForUpdate->setRoles(self::TEST_USER_ROLES);
            $manager->persist($userForUpdate);
        }

        $manager->flush();
    }
}