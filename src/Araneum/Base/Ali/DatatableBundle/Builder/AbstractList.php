<?php

namespace Araneum\Base\Ali\DatatableBundle\Builder;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Araneum\Bundle\UserBundle\Entity\User;

/**
 * Class AbstractList
 *
 * @package Araneum\Base\Ali\DatatableBundle\Builder
 */
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
     * @param object $doctrine
     * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
     */
    abstract public function createQueryBuilder($doctrine);

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
