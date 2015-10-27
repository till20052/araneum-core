<?php

namespace Araneum\Bundle\MainBundle\DataFixtures\ORM;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;

class ConnectionData extends AbstractFixture implements FixtureInterface
{
    const CONN_DB_TYPE      = 1;
    const CONN_DB_BASE_NAME = 'ultratrade_db';
    const CONN_DB_HOST      = 'localhost';
    const CONN_DB_PORT      = 5432;
    const CONN_DB_USERNAME  = 'bamboo';
    const TEST_CONN_DB_PASS = 'hu8jmn3';

    const CONN_HOST_TYPE     = 2;
    const CONN_HOST_NAME     = 'Ultatrade_Host';
    const CONN_HOST          = '192.168.70.221';
    const CONN_HOST_USERNAME = 'user';
    const CONN_HOST_PASSWORD = '123';

    const CONN_ENABLED = true;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $connectionHost = $manager->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(self::CONN_HOST_NAME);
        if (empty($connectionHost)) {
            $connectionHost = new Connection();
            $connectionHost->setType(self::CONN_HOST_TYPE);
            $connectionHost->setName(self::CONN_HOST_NAME);
            $connectionHost->setHost(self::CONN_HOST);
            $connectionHost->setEnabled(self::CONN_ENABLED);
            $connectionHost->setUserName(self::CONN_HOST_USERNAME);
            $connectionHost->setPassword(self::CONN_HOST_PASSWORD);
            $manager->persist($connectionHost);
            $manager->flush();
        }

        $connectionDb = $manager
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(self::CONN_DB_BASE_NAME);
        if (empty($connectionDb)) {
            $connectionDb = new Connection();
            $connectionDb->setType(self::CONN_DB_TYPE);
            $connectionDb->setName(self::CONN_DB_BASE_NAME);
            $connectionDb->setHost(self::CONN_DB_HOST);
            $connectionDb->setPort(self::CONN_DB_PORT);
            $connectionDb->setEnabled(self::CONN_ENABLED);
            $connectionDb->setUserName(self::CONN_DB_USERNAME);
            $connectionDb->setPassword(self::TEST_CONN_DB_PASS);
            $manager->persist($connectionDb);
            $manager->flush();
        }

        $this->addReference('connectionHost', $connectionHost);
        $this->addReference('connectionDb', $connectionDb);
    }
}