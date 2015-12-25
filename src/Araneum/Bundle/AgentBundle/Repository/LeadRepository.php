<?php

namespace Araneum\Bundle\AgentBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * LeadRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LeadRepository extends EntityRepository implements \Countable
{
    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('l')
                         ->select('COUNT(l.id) as cnt')
                         ->getQuery()
                         ->getOneOrNullResult()['cnt'];
    }

    /**
     * Find list of leads by email and/or phone as optionality
     *
     * @param  array $filters
     * @return array
     */
    public function findByFilter($filters = [])
    {
        $queryBuilder = $this->createQueryBuilder('l');

        if (isset($filters['email'])) {
            if (!preg_match('/[\w\d\.\-\@]{3,}/', $filters['email'])) {
                throw new InvalidParameterException('Email has not valid value');
            }

            $queryBuilder->where('l.email LIKE :email')
                ->setParameter('email', $filters['email'].'%');
        }

        if (isset($filters['phone'])) {
            if (!preg_match('/[0-9\-\(\)]{3,17}/', $filters['phone'])) {
                throw new InvalidParameterException('Phone has not valid value');
            }

            $queryBuilder->andWhere('l.phone LIKE :phone')
                ->setParameter('phone', $filters['phone'].'%');
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Get registered leads from all applications at last 24 hours
     *
     * @return array
     */
    public function getRegisteredLeadsFromApplicationsAtLast24Hours()
    {
        $qb = $this->createQueryBuilder('L');

        return $qb->select('A.name', 'DATE_PART(hour, L.createdAt) AS hours', 'COUNT(L) AS leadsCount')
            ->leftJoin('L.application', 'A')
            ->where(
                $qb->expr()->between('L.createdAt', ':start', ':end')
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
}
