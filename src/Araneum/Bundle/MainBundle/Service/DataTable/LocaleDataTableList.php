<?php

namespace Araneum\Bundle\MainBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Repository\LocaleRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LocaleDataTableList
 *
 * @package Araneum\Bundle\MainBundle\Service\DataTable
 */
class LocaleDataTableList extends AbstractList
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
     * @param ListBuilderInterface $builder
     * @return null
     */
    public function buildList(ListBuilderInterface $builder)
    {
        $builder
            ->add('id')
            ->add('name', ['search_type' => 'like'])
            ->add('locale', ['search_type' => 'like'])
            ->add('enabled')
            ->add(
                'orientation',
                [
                    'render' => function ($value, $data, $doctrine, $templating, $user) {
                        return $value != Locale::ORIENT_RGT_TO_LFT ? 'Left to right' : 'Right to left';
                    },
                ]
            )
            ->add('encoding', ['search_type' => 'like']);
    }

    /**
     * Returns the name of entity class.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return 'AraneumMainBundle:Locale';
    }

    /**
     * Create query builder
     *
     * @param Registry $doctrine
     * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
     */
    public function createQueryBuilder($doctrine)
    {
        /** @var LocaleRepository $repository */
        $repository = $doctrine->getRepository($this->getEntityClass());
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = $repository->getQueryBuilder();

            $filters = $this->container->get('form.factory')->create(
                $this->container->get('araneum_main.locale.filter.form')
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
