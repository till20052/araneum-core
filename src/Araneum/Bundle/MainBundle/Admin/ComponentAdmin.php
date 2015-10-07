<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class ComponentAdmin extends Admin
{
    /**
     * Create/Update Component Form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add(
                'name',
                'text',
                [
                    'label' => 'name'
                ]
            )
            ->add(
                'applications',
                'sonata_type_model',
                [
                    'multiple' => true,
                    'by_reference' => false,
                    'required' => false
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'label' => 'description',
                    'required' => false
                ]
            )
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false
                ]
            )
            ->add(
                'default',
                'checkbox',
                [
                    'label' => 'default',
                    'required' => false
                ]
            );
    }

    /**
     * Component List
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier(
                'id',
                null,
                [
                    'label' => 'ID'
                ]
            )
            ->add(
                'name',
                null,
                [
                    'label' => 'name',
                    'editable' => true
                ]
            )
            ->add(
                'applications',
                null,
                [
                    'labels' => 'applications',
                    'editable' => true
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'description',
                    'editable' => true
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                    'editable' => true
                ]
            )
            ->add(
                'default',
                null,
                [
                    'label' => 'default',
                    'editable' => true
                ]
            )
            ->add(
                'createdAt',
                'datetime',
                [
                    'label' => 'created_at',
                    'format' => 'm/d/Y'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'actions',
                    'actions' => [
                        'edit' => [],
                        'delete' => []
                    ]
                ]
            );
    }

    /**
     * Component Filters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add(
                'name',
                null,
                [
                    'label' => 'name'
                ]
            )
            ->add(
                'applications',
                null,
                [
                    'label' => 'applications'
                ]
            )
            ->add(
                'description',
                null,
                [
                    'label' => 'description'
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled'
                ]
            )
            ->add(
                'default',
                null,
                [
                    'label' => 'default'
                ]
            )
            ->add(
                'createdAt',
                'doctrine_orm_datetime_range',
                [
                    'label' => 'created_at',
                    'field_type' => 'sonata_type_datetime_range_picker'
                ],
                null,
                [
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy'
                ]
            );
    }
}