<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class LocaleAdmin
 *
 * @package Araneum\Bundle\MainBundle\Admin
 */
class LocaleAdmin extends Admin
{
    /**
     * Create Update locale
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', ['label' => 'name'])
            ->add('locale', 'text', ['label' => 'locale'])
            ->add(
                'enabled',
                'checkbox',
                [
                    'required' => false,
                    'label' => 'enabled',
                ]
            )
            ->add(
                'orientation',
                'choice',
                [
                    'label' => 'orientation',
                    'choices' => [
                        Locale::ORIENT_LFT_TO_RGT => 'left_to_right',
                        Locale::ORIENT_RGT_TO_LFT => 'right_to_left',
                    ],
                ]
            )
            ->add('encoding', 'text', ['label' => 'encoding']);
    }

    /**
     * Locale filters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, ['label' => 'name'])
            ->add('locale', null, ['label' => 'locale'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add(
                'orientation',
                'doctrine_orm_choice',
                [
                    'label' => 'orientation',
                ],
                'sonata_type_translatable_choice',
                [
                    'choices' => [
                        Locale::ORIENT_LFT_TO_RGT => 'left_to_right',
                        Locale::ORIENT_RGT_TO_LFT => 'right_to_left',
                    ],
                ]
            )
            ->add('encoding', null, ['label' => 'encoding'])
            ->add(
                'createdAt',
                'doctrine_orm_date_range',
                [
                    'field_type' => 'sonata_type_date_range_picker',
                    'label' => 'created_at',
                ],
                null,
                [
                    'format' => 'MM/dd/y',
                ]
            );
    }

    /**
     * Show locale list
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'id'])
            ->add(
                'name',
                'text',
                [
                    'label' => 'name',
                    'editable' => true,
                ]
            )
            ->add(
                'locale',
                'text',
                [
                    'label' => 'locale',
                    'editable' => true,
                ]
            )
            ->add(
                'enabled',
                null,
                [
                    'label' => 'enabled',
                    'editable' => true,
                ]
            )
            ->add(
                'orientation',
                'choice',
                [
                    'editable' => true,
                    'label' => 'orientation',
                    'choices' => [
                        Locale::ORIENT_LFT_TO_RGT => $this->getTranslator()->trans('left_to_right'),
                        Locale::ORIENT_RGT_TO_LFT => $this->getTranslator()->trans('right_to_left'),
                    ],
                ]
            )
            ->add(
                'encoding',
                'text',
                [
                    'label' => 'encoding',
                    'editable' => true,
                ]
            )
            ->add(
                'createdAt',
                'datetime',
                [
                    'label' => 'created_at',
                    'format' => 'm/d/Y',
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'label' => 'actions',
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ],
                ]
            );
    }
}
