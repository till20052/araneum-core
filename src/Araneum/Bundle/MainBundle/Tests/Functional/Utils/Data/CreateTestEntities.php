<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 25.09.15
 * Time: 9:44
 */

namespace Araneum\Bundle\MainBundle\Tests\Functional\Utils\Data;

use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;

class CreateTestEntities
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

}