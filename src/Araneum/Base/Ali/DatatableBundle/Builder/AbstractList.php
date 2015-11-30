<?php

namespace Araneum\Base\Ali\DatatableBundle\Builder;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Araneum\Bundle\UserBundle\Entity\User;

abstract class AbstractList
{
    /**
     * Build the list
     *
     * @param ListBuilderInterface $builder
     * @return null
     */
    abstract public function buildList(ListBuilderInterface $builder);

    /**
     * Create query builder
     *
     * @param Registry $doctrine
     * @param User     $user
     * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
     */
    public function createQueryBuilder(Registry $doctrine, $user = null)
    {
    }

    /**
     * Returns the name of entity class. Example:
     *
     * <code>
     *     return "AcmeUserBundle:User";
     * </code>
     *
     * @return string
     */
    abstract public function getEntityClass();
}
