<?php

namespace Araneum\Bundle\MailBundle\Service\DataTable;

use Araneum\Base\Ali\DatatableBundle\Builder\AbstractList;
use Araneum\Base\Ali\DatatableBundle\Builder\ListBuilderInterface;
use Araneum\Bundle\MailBundle\Entity\Mail;
use Araneum\Bundle\MailBundle\Repository\MailRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class MailDataTableList
 *
 * @package Araneum\Bundle\MailBundle\Service\DataTable
 */
class MailDataTableList extends AbstractList
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
     * MailDatatableList constructor.
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
                    'label' => 'mails.APPLICATION',
                ]
            )
            ->add(
                'sender',
                [
                    'search_type' => 'like',
                    'label' => 'mails.SENDER',
                ]
            )
            ->add(
                'target',
                [
                    'search_type' => 'like',
                    'label' => 'mails.TARGET',
                ]
            )
            ->add(
                'headline',
                [
                    'label' => 'mails.HEADLINE',
                ]
            )
            ->add(
                'status',
                [
                    'render' => function ($value) {
                        return Mail::$statuses[$value];
                    },
                    'label' => 'mails.STATUS',
                ]
            )
            ->add(
                'sentAt',
                [
                    'render' => function ($value) {
                        return $value instanceof \DateTime ? $value->format('Y-m-d h:i:s') : '';
                    },
                    'label' => 'mails.SENT_AT',
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
        return 'AraneumMailBundle:Mail';
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
         * @var MailRepository $repository
         */
        $repository = $doctrine->getRepository($this->getEntityClass());
        if (empty($this->queryBuilder)) {
            $this->queryBuilder = $repository->getQueryBuilder();

            $filters = $this->container->get('form.factory')->create(
                $this->container->get('araneum.mail.mail.filter.form')
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
