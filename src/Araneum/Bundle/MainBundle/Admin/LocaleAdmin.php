<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Entity\Locale;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

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
            ->add('name', 'text', ['label' => 'Name'])
            ->add('locale', 'text', ['label' => 'Locale'])
            ->add('enabled', null, ['required' => false])
            ->add(
                'orientation',
                'choice',
                [
                    'choices' => [
                        Locale::ORIENT_LFT_TO_RGT => 'Left to Right',
                        Locale::ORIENT_RGT_TO_LFT => 'Right to Left'
                    ]
                ]
            )
            ->add('encoding', 'text', ['label' => 'Encoding']);
    }

    /**
     * Locale filters
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name')
            ->add('locale')
            ->add('enabled')
            ->add('orientation')
            ->add('encoding')
            ->add('createdAt');
    }

    /**
     * Show locale list
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name', 'text', ['editable' => true])
            ->add('locale', 'text', ['editable' => true])
            ->add(
                'enabled',
                null,
                [
                    'required' => false,
                    'editable' => true,
                ]
            )
            ->add(
                'orientation',
                'choice',
                [
                    'choices' => [
                        Locale::ORIENT_LFT_TO_RGT => 'Left to Right',
                        Locale::ORIENT_RGT_TO_LFT => 'Right to Left'
                    ],
                    'editable' => true
                ]
            )
            ->add('encoding', 'text', ['editable' => true])
            ->add('createdAt')
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                    ]
                ]
            );
    }
}