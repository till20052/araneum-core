<?php

namespace Araneum\Bundle\MainBundle\Admin;

use Araneum\Bundle\MainBundle\Form\DataTransformer\ComponentOptionsTransformer;
use Araneum\Bundle\MainBundle\Form\Type\ComponentOptionType;
use Doctrine\Common\Collections\ArrayCollection;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ComponentAdmin extends Admin
{
    /**
     * Create/Update Component Form
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper
            ->getFormBuilder()
            ->addEventListener(
                FormEvents::POST_SET_DATA,
                function(FormEvent $event) use ($formMapper, $subject)
                {
                    $options = new ArrayCollection();

                    foreach($subject->getOptions() as $key => $value)
                    {
                        $options[] = [
                            'key' => $key,
                            'value' => $value
                        ];
                    }

                    $event
                        ->getForm()
                        ->get('options')
                        ->setData($options);
                }
            );

        $formMapper
            ->add('name', 'text', ['label' => 'name'])
            ->add(
                'description',
                'textarea',
                [
                    'label' => 'description',
                    'required' => false
                ]
            )
            ->add(
                'options',
                'collection',
                [
                    'label' => 'options',
                    'type' => new ComponentOptionType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'options' => [
                        'label' => false
                    ]
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

        $formMapper
            ->get('options')
            ->addModelTransformer(new ComponentOptionsTransformer());
    }

    /**
     * Component List
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', null, ['label' => 'ID'])
            ->add(
                'name',
                null,
                [
                    'label' => 'name',
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
            ->add('name', null, ['label' => 'name'])
            ->add('description', null, ['label' => 'description'])
            ->add('enabled', null, ['label' => 'enabled'])
            ->add('default', null, ['label' => 'default'])
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