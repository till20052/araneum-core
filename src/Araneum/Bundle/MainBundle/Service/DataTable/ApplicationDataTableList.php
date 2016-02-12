<?php

namespace Araneum\Bundle\MainBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\MainBundle\Repository\ClusterRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ApplicationDataTableList
 *
 * @package Araneum\Bundle\MainBundle\Service\DataTable
 */
class ApplicationDataTableList extends AbstractList
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
     * ApplicationDataTableList constructor.
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
                'cluster',
                [
                    'search_type' => 'like',
                    'label' => 'Cluster',
                ]
            )
            ->add(
                'name',
                [
                    'search_type' => 'like',
                    'label' => 'Name',
                ]
            )
            ->add(
                'domain',
                [
                    'search_type' => 'like',
                    'label' => 'Domain',
                ]
            )
            ->add(
                'type',
                [
                    'label' => 'Type',
                ]
            )
            ->add(
                'status',
                [
                    'render' => function ($value) {
                        return Application::getStatusDescription($value);
                    },
                    'label' => 'Status',
                ]
            )
            ->add(
                'createdAt',
                [
                    'search_type' => 'datetime',
                    'render' => function ($value) {
                        return $value instanceof \DateTime ? $value->format('Y-m-d') : '';
                    },
                    'label' => 'Created at',
                ]
            )
        ;
    }

    /**
     * Returns the name of entity class.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return 'AraneumMainBundle:Application';
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
         * @var ApplicationRepository $repository
         */
        $repository = $doctrine->getRepository($this->getEntityClass());
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = $repository->getQueryBuilder();

            $filters = $this->container->get('form.factory')->create(
                $this->container->get('araneum_main.application.filter.form')
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
