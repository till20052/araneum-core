<?php

namespace Araneum\Bundle\MainBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ClusterDataTableList
 *
 * @package Araneum\Bundle\MainBundle\Service\DataTable
 */
class ClusterDataTableList extends AbstractList
{
    /**
     * Query Builder
     *
     * @var
     */
    private $queryBuilder;

    /**
     * Container
     *
     * @var
     */
    private $container;

    /**
     * UserDatatableList constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Build the list
     *
     * @param  ListBuilderInterface $builder
     * @return null
     */
    public function buildList(ListBuilderInterface $builder)
    {
        $builder
            ->add('id')
            ->add(
                'name',
                [
                    'search_type' => 'like',
                    'label' => 'clusters.NAME',
                ]
            )
            ->add(
                'type',
                [
                    'render' => function ($value) {
                        return Cluster::getTypeDescription($value);
                    },
                    'label' => 'clusters.TYPE',
                ]
            )
            ->add(
                'enabled',
                [
                    'label' => 'clusters.ENABLED',
                ]
            )
            ->add(
                'status',
                [
                    'render' => function ($value) {
                        return Cluster::getStatusDescription($value);
                    },
                    'label' => 'clusters.STATUS',
                ]
            );
    }

    /**
     * Returns the name of entity class.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return 'AraneumMainBundle:Cluster';
    }

    /**
     * Create query builder
     *
     * @param  Registry $doctrine
     * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
     */
    public function createQueryBuilder($doctrine)
    {
        /**
         * @var ClusterRepository $repository
         */
        $repository = $doctrine->getRepository($this->getEntityClass());
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = $repository->getQueryBuilder();

            $filters = $this->container->get('form.factory')->create(
                $this->container->get('araneum_main.cluster.filter.form')
            );

            if ($this->container->get('request')->query->has($filters->getName())) {
                $filters->submit($this->container->get('request')->query->get($filters->getName()));
                $this->container->get('lexik_form_filter.query_builder_updater')->addFilterConditions(
                    $filters,
                    $this->queryBuilder
                );
            }
        }

        return $this->queryBuilder;
    }
}
