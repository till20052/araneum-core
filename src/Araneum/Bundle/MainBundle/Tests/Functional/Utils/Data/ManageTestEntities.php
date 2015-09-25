<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 25.09.15
 * Time: 9:44
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional\Utils\Data;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Doctrine\ORM\EntityManager;

class ManageTestEntities
{

    /**
     * @param EntityManager $manager
     * @param array $filterName
     *      * array(['name' => 'functionalTestConnection']) or array(['name' => 'functionalTestConnection', 'enabled'=>true])
     *
     * @return Connection
     */
    public static function createConnection(EntityManager $manager, array $filterName){

        $connection = $manager->getRepository('AraneumMainBundle:Connection')->findOneBy($filterName);

        if(empty($connection)){
            $connection = new Connection();
            $connection->setHost('127.0.0.1');
            $connection->setName('functionalTestConnection');
            $connection->setEnabled(true);
            $connection->setPort('111');
            $connection->setUserName('testUser');
            $connection->setPassword('password');
            $connection->setType($connection::CONN_HOST);
            $manager->persist($connection);
            $manager->flush();
        }

        return $connection;
    }


    /**
     * @param EntityManager $manager
     * @param array $entityParam
     * @param array $connectionFilter
     * @return Cluster
     */
    public static function createCluster(EntityManager $manager, array $entityParam, array $connectionFilter){
        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')->findOneBy($entityParam);
        $connection = $manager->getRepository('AraneumMainBundle:Connection')->findOneBy($connectionFilter);


        if(empty($cluster)){
            $cluster = new Cluster();
            $cluster->setName($entityParam['name']);
            $cluster->setHost($connection);
            $cluster->setEnabled(true);
            $cluster->setType(Cluster::TYPE_MULTIPLE);
            $cluster->setStatus(Cluster::STATUS_ONLINE);
            $manager->persist($cluster);
            $manager->flush();
        }

        return $cluster;
    }

    /**
     * @param EntityManager $manager
     * @param array $filterName
     */
    public static function deleteClusterByName(EntityManager $manager, array $filterName){
        $cluster = $manager->getRepository('AraneumMainBundle:Cluster')->findOneBy($filterName);
        if(!empty($cluster)){
            $manager->remove($cluster);
            $manager->flush();
        }
    }

}