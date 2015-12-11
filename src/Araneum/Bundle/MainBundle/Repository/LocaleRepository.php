<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * LocaleRepository
 */
class LocaleRepository extends EntityRepository implements \Countable
{
    /**
     * Delete entities
     * @param array $idx
     */
    public function delete(array $idx)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->delete('AraneumMainBundle:Locale', 'l')
            ->andWhere($qb->expr()->in('l.id', ':idx'))
            ->setParameter('idx', $idx)
            ->getQuery()
            ->execute();
    }

    /**
     * Update enable/disable field
     *
     * @param array $idx
     * @param $state
     */
    public function updateEnabled(array $idx, $state)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->update('AraneumMainBundle:Locale', 'l')
            ->set('l.enabled', ':state')
            ->andWhere($qb->expr()->in('l.id', ':idx'))
            ->setParameter('state', $state)
            ->setParameter('idx', $idx)
            ->getQuery()
            ->execute();
    }

    /**
     * Return Locale Query Builder without any conditions
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->createQueryBuilder('l');
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return (int)$this->createQueryBuilder('l')
            ->select('COUNT(l.id) as cnt')
            ->getQuery()
            ->getOneOrNullResult()['cnt'];
    }
}