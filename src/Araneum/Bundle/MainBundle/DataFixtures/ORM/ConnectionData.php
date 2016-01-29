<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class ConnectionData
 *
 * @package Araneum\Bundle\MainBundle\DataFixtures\ORM
 */
class ConnectionData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->setDefault($manager);
        $this->setIxoption($manager);
    }

    /**
     * Default fixture Connection
     * @param ObjectManager $manager
     */
    private function setDefault(ObjectManager $manager)
    {
        $connectionHost = $manager->getRepository('AraneumMainBundle:Connection')
            ->findOneByName('Ultatrade_Host');
        if (empty($connectionHost)) {
            $connectionHost = new Connection();
            $connectionHost->setType(2);
            $connectionHost->setName('Ultatrade_Host');
            $connectionHost->setHost('192.168.70.221');
            $connectionHost->setPort(80);
            $connectionHost->setEnabled(true);
            $connectionHost->setUserName('user');
            $connectionHost->setPassword('123');
            $connectionHost->setRunner($this->getReference('runner'));
            $manager->persist($connectionHost);
            $manager->flush();
        }

        $connectionDb = $manager
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName('ultratrade_db');
        if (empty($connectionDb)) {
            $connectionDb = new Connection();
            $connectionDb->setType(1);
            $connectionDb->setName('ultratrade_db');
            $connectionDb->setHost('localhost');
            $connectionDb->setPort(5432);
            $connectionDb->setEnabled(true);
            $connectionDb->setUserName('bamboo');
            $connectionDb->setPassword('hu8jmn3');
            $connectionDb->setRunner($this->getReference('runner'));
            $manager->persist($connectionDb);
            $manager->flush();
        }

        $this->addReference('connectionHost', $connectionHost);
        $this->addReference('connectionDb', $connectionDb);
    }

    /**
     * set add fixture Ixoption
     * @param ObjectManager $manager
     */
    private function setIxoption(ObjectManager $manager)
    {
        $connectionHost = $manager->getRepository('AraneumMainBundle:Connection')
            ->findOneByName('ixoption_host');
        if (empty($connectionHost)) {
            $connectionHost = new Connection();
            $connectionHost->setType(2);
            $connectionHost->setName('ixoption_host');
            $connectionHost->setHost('192.168.70.221');
            $connectionHost->setPort(80);
            $connectionHost->setEnabled(true);
            $connectionHost->setUserName('user');
            $connectionHost->setPassword('123');
            $connectionHost->setRunner($this->getReference('runnerIxoption'));
            $manager->persist($connectionHost);
            $manager->flush();
        }

        $connectionDb = $manager
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName('ixoption_db');
        if (empty($connectionDb)) {
            $connectionDb = new Connection();
            $connectionDb->setType(1);
            $connectionDb->setName('ixoption_db');
            $connectionDb->setHost('localhost');
            $connectionDb->setPort(5432);
            $connectionDb->setEnabled(true);
            $connectionDb->setUserName('bamboo');
            $connectionDb->setPassword('hu8jmn3');
            $connectionDb->setRunner($this->getReference('runnerIxoption'));
            $manager->persist($connectionDb);
            $manager->flush();
        }

        $this->addReference('connHostIxoption', $connectionHost);
        $this->addReference('connDBIxoption', $connectionDb);
    }

    /**
     * {@inheritDoc}
     */
    public function getDependencies()
    {
        return [
            'Araneum\Bundle\MainBundle\DataFixtures\ORM\RunnerData',
        ];
    }
}
