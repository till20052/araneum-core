<?php

namespace Araneum\Bundle\AgentBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * CustomerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CustomerRepository extends EntityRepository implements \Countable
{
    /**
     * Get Registered Customers
     *
     * @return array
     */
    public function getRegisteredCustomersFromApplications()
    {
        $qb = $this->createQueryBuilder('C');

        return $qb->select('A.name', 'DATE_PART(hour, C.createdAt) AS hours', 'COUNT(C) AS customers')
            ->leftJoin('C.application', 'A')
            ->where(
                $qb->expr()->between('C.createdAt', ':start', ':end')
            )
            ->groupBy('hours', 'A.name')
            ->setParameters(
                [
                    'start' => date('Y-m-d H:i:s', time() - 86400),
                    'end' => date('Y-m-d H:i:s', time()),
                ]
            )
            ->getQuery()
            ->getResult();
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('c')
                         ->select('COUNT(c.id) as cnt')
                         ->getQuery()
                         ->getOneOrNullResult()['cnt'];
    }
}
