<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ApplicationData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    const APP_NAME     = 'Ultratrade';
    const APP_DOMAIN   = 'ultratrade.com';
    const APP_PUBLIC   = true;
    const APP_ENABLED  = true;
    const APP_STATUS   = 1;
    const APP_TEMPLATE = 'DefaultTemplate';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $app = $manager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName(self::APP_NAME);

        if (empty($app)) {
            $app = new Application();
            $app->setName(self::APP_NAME);
            $app->setDomain(self::APP_DOMAIN);
            $app->setPublic(self::APP_PUBLIC);
            $app->setEnabled(self::APP_ENABLED);
            $app->setStatus(self::APP_STATUS);
            $app->setTemplate(self::APP_TEMPLATE);
            $app->setCluster($this->getReference('cluster'));
            $app->setDb($this->getReference('connectionDb'));
            $app->setLocales(new ArrayCollection([$this->getReference('locale')]));
            $app->setOwner($this->getReference('defaultOwnerApplication'));
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
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\LocaleData',
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ConnectionData',
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ClusterData',
            'Araneum\Bundle\UserBundle\DataFixtures\ORM\UserData'
        ];
    }
}