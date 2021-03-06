<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Doctrine\Common\Collections\ArrayCollection;
use Araneum\Bundle\MainBundle\Entity\Application;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ApplicationData
 *
 * @package Araneum\Bundle\MainBundle\DataFixtures\ORM
 */
class ApplicationData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->setDefault($manager);
        $this->setIxoption($manager);
        $this->setTradersBot($manager);
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
            'Araneum\Bundle\UserBundle\DataFixtures\ORM\UserData',
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\ComponentData',
        ];
    }

    /**
     * Default load Application fixture
     * @param ObjectManager $manager
     */
    private function setDefault(ObjectManager $manager)
    {
        $app = $manager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName('Ultratrade');

        if (empty($app)) {
            $app = new Application();
            $app->setName('Ultratrade');
            $app->setDomain('ultratrade.office.dev');
            $app->setPublic(true);
            $app->setEnabled(true);
            $app->setStatus(Application::STATUS_OK);
            $app->setTemplate('DefaultTemplate');
            $app->setCluster($this->getReference('cluster'));
            $app->setDb($this->getReference('connectionDb'));
            $app->setLocales(new ArrayCollection([$this->getReference('locale')]));
            $app->setOwner($this->getReference('userAdmin'));
            $app->setComponents(new ArrayCollection([$this->getReference('component')]));
            $app->setSpotApiPublicUrl('https://spotplatform.ultratrade.com');
            $app->setAppKey('f2481f3c3d2d7e9d9669e1ec3a3e01d30785270c563b60a417de93.70304637');
            $app->setSpotApiUrl('http://api-spotplatform.ultratrade.com/Api');
            $app->setSpotApiUser('araneum_n');
            $app->setSpotApiPassword('Ow50KQdh0t');
            $manager->persist($app);
            $manager->flush();
        }
        $this->addReference('application', $app);
    }

    /**
     * set Ixoption add fixture
     * @param ObjectManager $manager
     */
    private function setIxoption(ObjectManager $manager)
    {
        $app = $manager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName('ixoption');

        if (empty($app)) {
            $app = new Application();
            $app->setName('ixoption');
            $app->setDomain('ixoption.office.dev');
            $app->setPublic(true);
            $app->setEnabled(true);
            $app->setStatus(Application::STATUS_OK);
            $app->setTemplate('DefaultTemplate');
            $app->setCluster($this->getReference('cluster'));
            $app->setDb($this->getReference('connectionDb'));
            $app->setLocales(new ArrayCollection([$this->getReference('locale')]));
            $app->setOwner($this->getReference('userAdmin'));
            $app->setSpotApiPublicUrl('https://spotplatform.ixoption.com');
            $app->setAppKey('cb678b70df4d0e2ad5b7eb8688a7df186cc49cf056af25ff047a91.81106394');
            $app->setSpotApiUrl('http://api-spotplatform.ixoption.com/Api');
            $app->setSpotApiUser('araneum');
            $app->setSpotApiPassword('wU7tc2YKg2');
            $manager->persist($app);
            $manager->flush();
        }
        $this->addReference('appIxoption', $app);
    }

    /**
     * set Tradersbot add fixture
     * @param ObjectManager $manager
     */
    private function setTradersBot(ObjectManager $manager)
    {
        $app = $manager
            ->getRepository('AraneumMainBundle:Application')
            ->findOneByName('Tradersbot');

        if (empty($app)) {
            $app = new Application();
            $app->setName('Tradersbot');
            $app->setDomain('tradersbot.com');
            $app->setPublic(true);
            $app->setEnabled(true);
            $app->setStatus(Application::STATUS_OK);
            $app->setTemplate('DefaultTemplate');
            $app->setCluster($this->getReference('cluster'));
            $app->setDb($this->getReference('connectionDb'));
            $app->setLocales(new ArrayCollection([$this->getReference('locale')]));
            $app->setOwner($this->getReference('userAdmin'));
            $app->setComponents(new ArrayCollection([$this->getReference('component')]));
            $app->setSpotApiPublicUrl('https://spotplatform.tradersbot.com');
            $app->setAppKey('dc2e413437737725eab936a0d6c9532e507cec7156cc63f1bbd4e1.01384540');
            $app->setSpotApiUrl('http://api-spotplatform.tradersbot.com/Api');
            $app->setSpotApiUser('araneum');
            $app->setSpotApiPassword('wU7tc2YKg2');
            $manager->persist($app);
            $manager->flush();
        }
        $this->addReference('appTradersbot', $app);
    }
}
