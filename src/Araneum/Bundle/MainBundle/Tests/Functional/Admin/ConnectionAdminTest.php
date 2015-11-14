<?php

namespace Araneum\Bundle\MainBundle\Tests\Functional;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\ClusterFixtures;
use Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures;
use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\Common\Collections\ArrayCollection;

class ConnectionAdminTest extends BaseAdminController
{
    protected $listRoute       = 'admin_araneum_main_connection_list';
    protected $createRoute = 'admin_araneum_main_connection_create';
    protected $updateRoute = 'admin_araneum_main_connection_edit';
    protected $deleteRoute = 'admin_araneum_main_connection_delete';
    protected $checkRoute      = 'araneum_main_admin_connection_testConnection';
    protected $batchCheckRoute = 'araneum_main_admin_connection_batchAction';


    /**
     * Set up Before class
     */
    public static function setUpBeforeClass()
    {
        $client = static::createClient();
        $manager = $client->getContainer()
            ->get('doctrine.orm.entity_manager');

        $repository = $manager
            ->getRepository('AraneumMainBundle:Connection');

        $delete = $repository->findOneByName(ConnectionFixtures::TEST_CONN_FREE_NAME);

        if(!$delete){
            $delete = new Connection();
            $delete->setName(ConnectionFixtures::TEST_CONN_FREE_NAME)
                ->setHost('192.168.5.5')
                ->setPassword('123')
                ->setPort(123)
                ->setStatus(1)
                ->setUserName('user')
                ->setType(1);

            $manager->persist($delete);
            $manager->flush();
        }
    }

    /**
     * Delete entities after tests
     */
    public static function tearDownAfterClass()
    {
        $client = static::createClient();
        $manager = $client->getContainer()
            ->get('doctrine.orm.entity_manager');

        $repository = $manager
            ->getRepository('AraneumMainBundle:Connection');

        $functionalTestConnection = $repository->findOneByName('functionalTestConnection');

        if($functionalTestConnection){
            $manager->remove($functionalTestConnection);
            $manager->flush();
        }

    }

        /**
     * Test check action
     *
     */
    public function testStatusChecker()
    {
        $client = $this->createAdminAuthorizedClient();

        $connection = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_NAME);

        $crawler = $client->request(
            'GET',
            $client->getContainer()->get('router')->generate(
                $this->checkRoute,
                [
                    'id' => $connection->getId(),
                    '_locale' => 'en'
                ]
            )
        );

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    /**
     * Test batch check status
     *
     */
    public function testBatchStatusChecker()
    {
        $client = $this->createAdminAuthorizedClient();

        $crawler = $client->request(
            'POST',
            $client->getContainer()->get('router')->generate(
                $this->listRoute,
                [
                    '_locale' => 'en'
                ]
            )
        );

        $form = $crawler->selectButton('OK')->form();
        $form['action'] = 'checkStatus';
        $form['all_elements'] = 'on';

        $crawler = $client->submit($form);

        $form = $crawler->selectButton('Yes, execute')->form();
        $client->submit($form);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }


    /**
     */
    public function filterDataSource()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneBy(['name' => 'TestDbConnection']);

        return
            [
                [
                    'Filter with good data' => [
                        'filter[type][type]' => 1,
                        'filter[name][value]' => 'TestDbConnection',
                        'filter[host][value]' => '192.168.1.200',
                        'filter[port][value]' => 4321,
                        'filter[userName][value]' => 'TestDbConnectionUserName',
                        'filter[enabled][value]' => true,
                        'filter[createdAt][value][start]' => '01/01/1970',
                        'filter[createdAt][value][end]' => '01/01/2040',
                        'filter[updatedAt][value][start]' => '01/01/1970',
                        'filter[updatedAt][value][end]' => '01/01/2040'
                    ],
                    true,
                    $connection,
                    'Filter with bad data' => [
                        'filter[type][type]' => 1,
                        'filter[name][value]' => 'TestDbConnection',
                        'filter[host][value]' => '192.168.1.200',
                        'filter[port][value]' => 4321,
                        'filter[userName][value]' => 'TestDbConnectionUserName',
                        'filter[enabled][value]' => false,
                        'filter[createdAt][value][start]' => '01/01/1970',
                        'filter[createdAt][value][end]' => '01/01/2040',
                        'filter[updatedAt][value][start]' => '01/01/1970',
                        'filter[updatedAt][value][end]' => '01/01/2040'
                    ],
                    false,
                    $connection
                ]
            ];
    }

    /**
     */
    public function createDataSource()
    {
        return
            [
                [
                    'Good create' => [
                        'type' => 1,
                        'name' => 'functionalTestConnection',
                        'host' => '127.0.0.1',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    true,
                    'Unique create test' => [
                        'type' => 1,
                        'name' => 'functionalTestConnection',
                        'host' => '127.0.0.1',
                        'port' => 1111,
                        'user' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    false,
                    'Port must be integer' => [
                        'type' => 1,
                        'name' => 'testNameAnother',
                        'host' => 'testHost',
                        'port' => 'string',
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    false,
                    'Min length of password' => [
                        'type' => 1,
                        'name' => 'testNameAnotherNew',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'no'
                    ],
                    false
                ]
            ];
    }

    /**
     */
    public function updateDataSource()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneBy(['name' => 'TestConnection']);

        return
            [
                [
                    'Update without changes' => [
                        'type' => 1,
                        'name' => 'TestConnection',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    true,
                    $connection,
                    'Update name' => [
                        'type' => 2,
                        'name' => 'testNameAnother',
                        'host' => 'testHost',
                        'port' => 'string',
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'testPassword'
                    ],
                    false,
                    $connection,
                    'Update with min length password' => [
                        'type' => 3,
                        'name' => 'testNameAnotherNew',
                        'host' => 'testHost',
                        'port' => 1111,
                        'userName' => 'testUserName',
                        'enabled' => true,
                        'password' => 'no'
                    ],
                    false,
                    $connection
                ]
            ];
    }

    /**
     */
    public function deleteDataSource()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_FREE_NAME);

        return $connection;
    }

    /**
     * Test persist connection
     *
     */
    public function testDeletePersistConnection()
    {
        $connection = self::createClient()
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_DB_NAME);

        $client = $this->createAdminAuthorizedClient();

        $crawler = $client->request(
            'GET',
            $client->getContainer()
                ->get('router')
                ->generate(
                    $this->deleteRoute,
                    [
                        'id' => $connection->getId(),
                        '_locale' => 'en',
                    ]
                )
        );

        $form = $crawler->selectButton('Yes, delete')->form();
        $client->submit($form, ['_method' => 'DELETE']);

        $entityFromDb = $client
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository(get_class($connection))
            ->find($connection->getId());

        $this->assertFalse(empty($entityFromDb));
    }

}