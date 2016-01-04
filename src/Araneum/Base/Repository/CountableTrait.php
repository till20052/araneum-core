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
        $result = $this->createQueryBuilder('REPO')
            ->select('COUNT(REPO.id) as repoCount')
            ->getQuery()
            ->getOneOrNullResult();

        if (is_null($result)) {
            $result = ['repoCount' => 0];
        }

        return (int) $result['repoCount'];
    }
}
