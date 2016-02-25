<?php

namespace Araneum\Bundle\AgentBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\AgentBundle\Entity\Lead;
use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Repository\ApplicationRepository;
use Araneum\Bundle\AgentBundle\Repository\LeadRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LeadDataTableList
 *
 * @package Araneum\Bundle\AgentBundle\Service\DataTable
 */
class LeadDataTableList extends AbstractList
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
     * LeadDataTableList constructor.
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
                'application.name',
                [
                    'search_type' => 'like',
                    'label' => 'leads.APPLICATION_NAME',
                ]
            )
            ->add(
                'firstName',
                [
                    'search_type' => 'like',
                    'label' => 'leads.FIRST_NAME',
                ]
            )
            ->add(
                'lastName',
                [
                    'search_type' => 'like',
                    'label' => 'leads.LAST_NAME',
                ]
            )
            ->add(
                'phone',
                [
                    'search_type' => 'like',
                    'label' => 'leads.PHONE',
                ]
            )
            ->add(
                'email',
                [
                    'search_type' => 'like',
                    'label' => 'leads.EMAIL',
                ]
            )
            ->add(
                'appKey',
                [
                    'search_type' => 'like',
                    'label' => 'leads.APP_KEY',
                ]
            )
            ->add(
                'createdAt',
                [
                    'render' => function ($value) {
                        return $value instanceof \DateTime ? $value->format('Y-m-d') : '';
                    },
                    'label' => 'leads.CREATED_AT',
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
        return 'AraneumAgentBundle:Lead';
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
         * @var LeadRepository $repository
         */
        $repository = $doctrine->getRepository($this->getEntityClass());
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = $repository->getQueryBuilder();

            $filters = $this->container->get('form.factory')->create(
                $this->container->get('araneum_agent.lead.filter.form')
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
