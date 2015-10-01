<?php

namespace Araneum\Base\Tests\Fixtures\Main;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ConnectionFixtures extends AbstractFixture implements FixtureInterface
{
    const TEST_CONN_DB_TYPE = 1;
    const TEST_CONN_HOST_TYPE = 2;
    const TEST_CONN_NAME = 'TestConnection';
    const TEST_CONN_DB_NAME = 'TestDbConnection';
    const TEST_CONN_HOST = '192.168.1.200';
    const TEST_CONN_PORT = 1234;
    const TEST_CONN_DB_PORT = 4321;
    const TEST_CONN_ENABLED = true;
    const TEST_CONN_USERNAME = 'TestConnectionUserName';
    const TEST_CONN_DB_USERNAME = 'TestDbConnectionUserName';
    const TEST_CONN_PASS = 'TestConnectionPassword';
    const TEST_CONN_DB_PASS = 'TestDbConnectionPassword';

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $connectionHost = $manager
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(self::TEST_CONN_HOST_TYPE);

        $connectionDb = $manager
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(self::TEST_CONN_DB_TYPE);

        if (empty($connection)) {
            $connectionHost = new Connection();
            $connectionHost->setType(self::TEST_CONN_HOST_TYPE);
            $connectionHost->setName(self::TEST_CONN_NAME);
            $connectionHost->setHost(self::TEST_CONN_HOST);
            $connectionHost->setPort(self::TEST_CONN_PORT);
            $connectionHost->setEnabled(self::TEST_CONN_ENABLED);
            $connectionHost->setUserName(self::TEST_CONN_USERNAME);
            $connectionHost->setPassword(self::TEST_CONN_PASS);

            $connectionDb = new Connection();
            $connectionDb->setType(self::TEST_CONN_DB_TYPE);
            $connectionDb->setName(self::TEST_CONN_DB_NAME);
            $connectionDb->setHost(self::TEST_CONN_HOST);
            $connectionDb->setPort(self::TEST_CONN_DB_PORT);
            $connectionDb->setEnabled(self::TEST_CONN_ENABLED);
            $connectionDb->setUserName(self::TEST_CONN_DB_USERNAME);
            $connectionDb->setPassword(self::TEST_CONN_DB_PASS);

            $manager->persist($connectionHost);
            $manager->persist($connectionDb);
            $manager->flush();
        }
        $this->addReference('connectionHost', $connectionHost);
        $this->addReference('connectionDb', $connectionDb);
    }
}