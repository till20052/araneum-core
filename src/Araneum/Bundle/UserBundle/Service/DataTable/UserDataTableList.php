<?php

namespace Araneum\Bundle\UserBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MainBundle\Entity\Locale;
use Araneum\Bundle\MainBundle\Repository\LocaleRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\EntityManager;

/**
 * Class UserDataTableList
 *
 * @package Araneum\Bundle\UserBundle\Service\DataTable
 */
class UserDataTableList extends AbstractList
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
                'email',
                [
                'search_type' => 'like',
                'label' => 'email',
                ]
            )
            ->add(
                'username',
                [
                'search_type' => 'like',
                'label' => 'user.LOGIN',
                ]
            )
            ->add(
                'fullName',
                [
                'search_type' => 'like',
                'label' => 'user.FULLNAME',
                ]
            )
            ->add(
                'enabled',
                [
                'label' => 'admin.general.ENABLE',
                ]
            )
            ->add(
                'roles.name',
                [
                'search_type' => 'like',
                'label' => 'user.ROLE',
                ]
            )
            ->add(
                'lastLogin',
                [
                    'search_type' => 'datetime',
                    'render' => function ($value) {
                        return $value instanceof \DateTime ? $value->format('Y-m-d') : '';
                    },
                    'label' => 'user.LAST_LOGIN',
                ]
            )
            ->add(
                'useLdap',
                [
                    'render' => function ($value) {
                        return ($value)?'<em class="icon-check fa-2x"></em>':'<em class="icon-ban fa-2x"></em>';
                    },
                    'label' => 'admin.general.USE_LDAP',
                ]
            )
            ->add(
                'createdAt',
                [
                    'label' => 'user.REGISTER',
                    'search_type' => 'datetime',
                ]
            )
            ->setOrderBy('createdAt', 'desc')
            ->setSearch(true);
    }

    /**
     * Returns the name of entity class.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return 'AraneumUserBundle:User';
    }

    /**
     * Create query builder
     *
     * @param  EntityManager $em
     * @return \Ali\DatatableBundle\Util\Factory\Query\QueryInterface
     */
    public function createQueryBuilder($em)
    {
        $repository = $em->getRepository($this->getEntityClass());
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = $repository->getQueryBuilder();

            $filters = $this->container->get('form.factory')->create(
                $this->container->get('araneum_user.user.filter.form')
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
