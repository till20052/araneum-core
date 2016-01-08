<?php

namespace Araneum\Bundle\MainBundle\Repository;

use Araneum\Base\Repository\CountableTrait;
use Doctrine\ORM\EntityRepository;

/**
 * LocaleRepository
 */
class LocaleRepository extends EntityRepository implements \Countable
{
    use CountableTrait;

    /**
     * Delete entities
     *
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
     * @param mixed $state
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
}
