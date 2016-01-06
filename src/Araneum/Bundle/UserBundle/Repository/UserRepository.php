<?php
namespace Araneum\Bundle\UserBundle\Repository;

use Araneum\Base\Repository\CountableTrait;
use Doctrine\ORM\EntityRepository;

/**
 * Class UserRepository
 *
 * @package Araneum\Bundle\UserBundle\Repository
 */
class UserRepository extends EntityRepository implements \Countable
{
    use CountableTrait;

    /**
     * Return Locale Query Builder without any conditions
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder()
    {
        return $this->createQueryBuilder('u');
    }
}
