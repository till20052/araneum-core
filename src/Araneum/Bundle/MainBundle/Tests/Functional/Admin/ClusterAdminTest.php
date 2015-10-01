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
use Araneum\Bundle\MainBundle\Tests\Functional\Utils\Data;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\ORM\EntityNotFoundException;

class ClusterAdminTest extends BaseAdminController
{
    protected $createRoute = 'admin_araneum_main_cluster_create';
    protected $updateRoute = 'admin_araneum_main_cluster_edit';
    protected $deleteRoute = 'admin_araneum_main_cluster_delete';
    protected $listRoute = 'admin_araneum_main_cluster_list';

    const CLUSTER_TEST_NAME = 'TestCluTmp';

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
                    'status' => 1,
                    'enabled' => true
                ],
                true],
            'Check entity unique name' => [
                [
                    'name' => self::CLUSTER_TEST_NAME,
                    'hosts' => [$connection->getId()],
                    'type' => 2,
                    'status' => 1,
                    'enabled' => true
                ],
                false]
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
                    'filter[status][value]' => ClusterFixtures::TEST_CLU_STATUS,
                    'filter[type][value]' => ClusterFixtures::TEST_CLU_TYPE,
                    'filter[created_at][value][start]' => '01/01/1971',
                    'filter[created_at][value][end]' => date('m/d/Y', time() + 86400)
                ],
                true, $cluster],
            'Try find non exist entity' =>
            [
                [
                    'filter[name][value]' => md5(uniqid(null, true)),
                    'filter[hosts][value]' => $connection->getId(),
                    'filter[enabled][value]' => ClusterFixtures::TEST_CLU_ENABLED,
                    'filter[status][value]' => ClusterFixtures::TEST_CLU_STATUS,
                    'filter[type][value]' => ClusterFixtures::TEST_CLU_TYPE,
                    'filter[created_at][value][start]' => '01/01/1971',
                    'filter[created_at][value][end]' => date('m/d/Y', time() + 86400)
                ],
                false, $cluster]
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
        $cluster = static::createClient()->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('AraneumMainBundle:Cluster')
            ->findOneByName(ClusterFixtures::TEST_TEMP_CLU_NAME);

        return [
            'Update temporary entity to new name' => [
                [
                    'name' => self::CLUSTER_TEST_NAME . '1',
                    'type' => 2,
                    'status' => 1,
                    'enabled' => true
                ],
                true, $cluster],
            'Update temporary entity to exist name' => [
                [
                    'name' => ClusterFixtures::TEST_CLU_NAME,
                    'type' => 2,
                    'status' => 1,
                    'enabled' => true
                ],
                false, $cluster]
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