<?php

namespace Araneum\Base\Repository;

trait CountableTrait
{
    /**
     * Count elements of an object
     *
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return (int) $this->createQueryBuilder('REPO')
                         ->select('COUNT(REPO.id) as repoCount')
                         ->getQuery()
                         ->getOneOrNullResult()['repoCount'];
    }
}