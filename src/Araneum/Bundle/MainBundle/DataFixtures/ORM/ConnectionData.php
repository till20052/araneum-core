<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;

/**
 * Class ConnectionData
 *
 * @package Araneum\Bundle\MainBundle\DataFixtures\ORM
 */
class ConnectionData extends AbstractFixture implements FixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
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
            $manager->persist($connectionDb);
            $manager->flush();
        }

        $this->addReference('connectionHost', $connectionHost);
        $this->addReference('connectionDb', $connectionDb);
    }
}
