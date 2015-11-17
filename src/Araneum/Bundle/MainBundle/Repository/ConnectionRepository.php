<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Araneum\Base\Tests\Fixtures\Main\ConnectionFixtures;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr;

/**
 * Class ConnectionRepository for
 *
 * @package Araneum\Bundle\MainBundle\Repository
 */
class ConnectionRepository extends EntityRepository
{
    /**
     * Return query with unused connections for clusters
     *
     * @param $type
     * @return \Doctrine\ORM\Query
     */
    public function getQueryByUnusedAndType($type)
    {
        $qb = $this->createQueryBuilder('con');
        $qb
            ->leftJoin('AraneumMainBundle:Cluster', 'clu', Expr\Join::WITH, 'clu.host = con.id')
            ->where('con.type = :type')
            ->andWhere('con.enabled = true')
            ->andWhere('clu.id IS NULL')
            ->setParameters(['type' => $type]);

        return $qb->getQuery();
    }

    /**
     * Get Applications
     *
     * @param $id
     * @return ArrayCollection
     */
    public function getApplications($id)
    {
        $q = $this->createQueryBuilder('cn')
            ->select('a')
            ->innerJoin('cn.clusters', 'cl')
            ->innerJoin('AraneumMainBundle:Application', 'a', Expr\Join::WITH, 'a.cluster = cl.id')
            ->where('cn.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

        return new ArrayCollection($q->getResult());
    }

    /**
     * get active host connections
     *
     * @return array
     * @param $ids array
     */
    public function getActiveHostConnections(array $ids)
    {
        $qb = $this->createQueryBuilder('con')
            ->where('con.type = :type')
            ->andWhere('con.enabled = true')
            ->andWhere('con.id IN (:ids)')
            ->setParameters(
                [
                    'type' => Connection::CONN_HOST,
                    'ids' => $ids
                ]
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Find Connection by appKey
     *
     * @param $appKey
     * @return array
     */
    public function findConnectionByAppKey($appKey)
    {
        $qb = $this->createQueryBuilder('conn');

        $qb
            ->join('conn.clusters', 'clu')
            ->join('clu.applications', 'app')
            ->where('app.appKey = :appKey')
            ->setParameters(['appKey' => $appKey]);

        return $qb->getQuery()->getResult();
    }

    /**
     * Get host by cluster Id
     *
     * @param $clusterId
     * @return array
     */
    public function getHostByClusterId($clusterId)
    {
        $qb = $this->createQueryBuilder('conn');
        $qb
            ->join('conn.clusters', 'clu')
            ->where('clu.id = :clu')
            ->andWhere('conn.type = :type')
            ->andWhere('conn.enabled = true')
            ->setParameters(
                [
                    'clu' => $clusterId,
                    'type' => ConnectionFixtures::TEST_CONN_HOST_TYPE
                ]
            );

        return $qb->getQuery()->getResult();
    }
}