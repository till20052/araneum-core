<?php

namespace Araneum\Base\Tests\Fixtures\Main;

use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ApplicationFixtures extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    const TEST_APP_NAME = 'TestApplicationName';
    const TEST_APP_DOMAIN = 'test.domain.com';
    const TEST_APP_ALIASES = ['www.test.domain.com', 'www2.test.domain.com'];
    const TEST_APP_PUBLIC = true;
    const TEST_APP_ENABLED = true;
    const TEST_APP_STATUS = 1;
    const TEST_APP_TEMPLATE = 'TestTemplate';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $app = $manager->getRepository('AraneumMainBundle:Application')->findOneByName(self::TEST_APP_NAME);
        if (empty($app)) {
            $app = new Application();
            $app->setName(self::TEST_APP_NAME);
            $app->setDomain(self::TEST_APP_DOMAIN);
            $app->setAliases(self::TEST_APP_ALIASES);
            $app->setPublic(self::TEST_APP_PUBLIC);
            $app->setEnabled(self::TEST_APP_ENABLED);
            $app->setStatus(self::TEST_APP_STATUS);
            $app->setTemplate(self::TEST_APP_TEMPLATE);
            $app->setCluster($this->getReference('cluster'));
            $app->setDb($this->getReference('connectionDb'));
            $app->setLocale($this->getReference('locale'));
            $app->setComponents(new ArrayCollection([$this->getReference('component')]));
            $app->setOwner($this->getReference('owner'));
            $manager->persist($app);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            'Araneum\Base\Tests\Fixtures\Main\ClusterFixtures',
            'Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures',
            'Araneum\Base\Tests\Fixtures\Main\ComponentFixtures'
        ];
    }
}