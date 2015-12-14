<?php
namespace Araneum\Bundle\MailBundle\Admin;

use Araneum\Bundle\MailBundle\Entity\Mail;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

/**
 * Class MailAdmin
 *
 * @package Araneum\Bundle\MailBundle\Admin
 */
class MailAdmin extends Admin
{
    /**
     * Configure routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('araneum.mail.admin.mail.edit');
        $collection->remove('araneum.mail.admin.mail.create');
        $collection->remove('araneum.mail.admin.mail.delete');
    }

    /**
     * Filters for list
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('application', null, ['label' => 'application'])
            ->add('sender', null, ['label' => 'from'])
            ->add('target', null, ['label' => 'to'])
            ->add('headline', null, ['label' => 'headline'])
            ->add(
                'status',
                null,
                ['label' => 'status'],
                'choice',
                [
                    'choices' => [
                        Mail::STATUS_NEW => $this->trans('new'),
                        Mail::STATUS_PENDING => $this->trans('pending'),
                        Mail::STATUS_SENT => $this->trans('sent'),
                        Mail::STATUS_READ => $this->trans('read'),
                    ],

                ]
            )
            ->add(
                'sentAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'sent_at',
                ],
                null,
                ['format' => 'MM/dd/y']
            )
            ->add('textBody', null, ['label' => 'text_body'])
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'created_at',
                ],
                null,
                ['format' => 'MM/dd/y']
            );
    }

    /**
     * Show list
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id']);

        $this->setFieldsMapper($listMapper);

        $listMapper
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'show' => [],
                    ],
                    'label' => 'actions',
                ]
            );
    }

    /**
     * Show fields
     *
     * @param ShowMapper $formMapper
     */
    protected function configureShowFields(ShowMapper $formMapper)
    {
        $this->setFieldsMapper($formMapper);

        $formMapper
            ->add('textBody', null, ['label' => 'text_body'])
            ->add('htmlBody', 'html', ['label' => 'html_body'])
            ->add('attachment', 'link', ['label' => 'attachment'])
            ->add(
                'updatedAt',
                null,
                [
                    'format' => 'm/d/Y',
                    'label' => 'updated_at',
                ]
            );
    }

    /**
     *  Set same fields for show and list mappers
     *
     * @param $mapper
     */
    protected function setFieldsMapper($mapper)
    {
        $mapper
            ->add('application', null, ['label' => 'application'])
            ->add('sender', null, ['label' => 'from'])
            ->add('target', null, ['label' => 'to'])
            ->add('headline', null, ['label' => 'headline'])
            ->add(
                'status',
                'choice',
                [
                    'choices' => [
                        Mail::STATUS_NEW => $this->trans('new'),
                        Mail::STATUS_PENDING => $this->trans('pending'),
                        Mail::STATUS_SENT => $this->trans('sent'),
                        Mail::STATUS_READ => $this->trans('read'),
                    ],
                    'label' => 'status',
                ]
            )
            ->add('sentAt', null, ['label' => 'sent_at'])
            ->add(
                'createdAt',
                null,
                [
                    'format' => 'm/d/Y',
                    'label' => 'created_at',
                ]
            );
    }
}
