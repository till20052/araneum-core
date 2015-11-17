<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 24.09.15
 * Time: 16:40
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional\Admin;

use Araneum\Base\Tests\Controller\BaseAdminController;
use Araneum\Base\Tests\Fixtures\Main\ClusterFixtures;
use Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;

class ClusterAdminTest extends BaseAdminController
{
    const CLUSTER_TEST_NAME = 'TestCluTmp';
    protected $createRoute = 'admin_araneum_main_cluster_create';
    protected $updateRoute = 'admin_araneum_main_cluster_edit';
    protected $deleteRoute = 'admin_araneum_main_cluster_delete';
    protected $listRoute = 'admin_araneum_main_cluster_list';

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

        $connection = $repository->findOneByName(ConnectionFixtures::TEST_CONN_NAME);

        if (!$connection) {
            $connection = new Connection();
            $connection
                ->setName(ConnectionFixtures::TEST_CONN_NAME)
                ->setHost('192.168.5.5')
                ->setPassword('123')
                ->setPort(123)
                ->setStatus(1)
                ->setUserName('user')
                ->setType(1);

            $manager->persist($connection);
            $manager->flush();
        }

        $repository = $manager
            ->getRepository('AraneumMainBundle:Cluster');

        $delete = $repository->findOneByName(ClusterFixtures::DELETE_CLU_NAME);

        $create = $repository->findOneByName(self::CLUSTER_TEST_NAME);

        $update = $repository->findOneByName(ClusterFixtures::TEST_CLU_NAME);

        $clusterTmp = $repository
            ->findOneByName(ClusterFixtures::TEST_TEMP_CLU_NAME . '1');

        if ($clusterTmp) {
            $manager->remove($clusterTmp);
            $manager->flush();
        }

        if ($create) {
            $manager->remove($create);
            $manager->flush();
        }

        if (!$delete) {
            $delete = new Cluster();
            $delete->setName(ClusterFixtures::DELETE_CLU_NAME)
                ->setHosts(new ArrayCollection([$connection]))
                ->setType(1)
                ->setStatus(Cluster::STATUS_OK);


            $manager->persist($delete);
            $manager->flush();
        }

        if (!$update) {
            $update = new Cluster();
            $update->setName(ClusterFixtures::TEST_CLU_NAME)
                ->setType(Cluster::STATUS_OK)
                ->setStatus(ClusterFixtures::TEST_CLU_STATUS)
                ->setHosts(new ArrayCollection([$connection]))
                ->setEnabled(ClusterFixtures::TEST_CLU_ENABLED);

            $manager->persist($update);
            $manager->flush();
        }

    }

    /**
     * Set data for create entity
     *
     * @return array
     * @throws EntityNotFoundException
     */
    public function createDataSource()
    {
        $connection = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_NAME);


        if (!isset($connection)) {
            throw new EntityNotFoundException('Connection entity not found');
        }

        return [
            'Create entity' => [
                [
                    'name' => self::CLUSTER_TEST_NAME,
                    'hosts' => [$connection->getId()],
                    'type' => 2,
                    'status' => Cluster::STATUS_OK,
                    'enabled' => true
                ],
                true
            ],
            'Check entity unique name' => [
                [
                    'name' => self::CLUSTER_TEST_NAME,
                    'hosts' => [$connection->getId()],
                    'type' => 2,
                    'status' => Cluster::STATUS_OK,
                    'enabled' => true
                ],
                false
            ]
        ];
    }

    /**
     * Set data for Filter test
     *
     * @return array
     * @throws EntityNotFoundException
     */
    public function filterDataSource()
    {
        $connection = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Connection')
            ->findOneByName(ConnectionFixtures::TEST_CONN_NAME);

        $cluster = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(ClusterFixtures::TEST_CLU_NAME);

        return [
            'Try find entity fixture' => [
                [
                    'filter[name][value]' => ClusterFixtures::TEST_CLU_NAME,
                    'filter[hosts][value]' => $connection->getId(),
                    'filter[enabled][value]' => ClusterFixtures::TEST_CLU_ENABLED,
                    //'filter[status][value]' => Cluster::STATUS_OK,
                    'filter[type][value]' => ClusterFixtures::TEST_CLU_TYPE,
                    'filter[createdAt][value][start]' => '01/01/1971',
                    'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
                ],
                true,
                $cluster
            ],
            'Try find non exist entity' => [
                [
                    'filter[name][value]' => md5(uniqid(null, true)),
                    'filter[hosts][value]' => $connection->getId(),
                    'filter[enabled][value]' => ClusterFixtures::TEST_CLU_ENABLED,
                    'filter[status][value]' => ClusterFixtures::TEST_CLU_STATUS,
                    'filter[type][value]' => ClusterFixtures::TEST_CLU_TYPE,
                    'filter[createdAt][value][start]' => '01/01/1971',
                    'filter[createdAt][value][end]' => date('m/d/Y', time() + 86400)
                ],
                false,
                $cluster
            ]
        ];
    }

    /**
     * Return data for update method
     *
     * @return array
     * @throws EntityNotFoundException
     */
    public function updateDataSource()
    {
        $manager = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager');

        $cluster = $manager
            ->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(ClusterFixtures::TEST_TEMP_CLU_NAME);

        if (!$cluster) {

            $connection = $manager
                ->getRepository('AraneumMainBundle:Connection')
                ->findOneByName(ConnectionFixtures::TEST_CONN_FREE_NAME);

            if (!$connection) {
                $connection = new Connection();
                $connection->setName(ConnectionFixtures::TEST_CONN_FREE_NAME)
                    ->setHost('192.168.5.5')
                    ->setPassword('123')
                    ->setPort(123)
                    ->setStatus(1)
                    ->setUserName('user')
                    ->setType(2);

                $manager->persist($connection);
                $manager->flush();
            }

            $cluster = new Cluster();
            $cluster->setName(ClusterFixtures::TEST_TEMP_CLU_NAME)
                ->setHosts(new ArrayCollection([$connection]))
                ->setType(ClusterFixtures::TEST_CLU_TYPE)
                ->setStatus(Cluster::STATUS_OK)
                ->setEnabled(ClusterFixtures::TEST_CLU_ENABLED);
            $manager->persist($cluster);
            $manager->flush();
        }

        return [
            'Update temporary entity to new name' => [
                [
                    'name' => ClusterFixtures::TEST_TEMP_CLU_NAME . '1',
                    'type' => 2,
                    'status' => Cluster::STATUS_OK,
                    'enabled' => true
                ],
                true,
                $cluster
            ],
            'Update temporary entity to exist name' => [
                [
                    'name' => self::CLUSTER_TEST_NAME,
                    'type' => 2,
                    'status' => Cluster::STATUS_OK,
                    'enabled' => true
                ],
                false,
                $cluster
            ]
        ];
    }

    /**
     * Return entity for testDelete method
     *
     * @return mixed
     */
    public function deleteDataSource()
    {
        $cluster = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(ClusterFixtures::DELETE_CLU_NAME);

        return $cluster;
    }
}