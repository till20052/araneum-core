<?php
namespace Araneum\Bundle\UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{
    protected $formOptions = ['validation_groups' => ['Profile']];

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('email', null, ['label' => 'email'])
            ->add('username', null, ['label' => 'username'])
            ->add('fullName', null, ['label' => 'fullName'])
            ->add(
                'enabled',
                'checkbox',
                [
                    'label' => 'enabled',
                    'required' => false,
                ]
            )
            ->add('roles', null, ['label' => 'roles']);

        if ($this->getSubject()->getId() === null) {
            $formMapper->add('plainPassword', 'text', ['label' => 'password']);
        }
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('fullName', null, ['label' => 'fullName'])
            ->add('email', null, ['label' => 'email'])
            ->add('enabled', null, ['label' => 'enabled'])
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

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('email', null, ['label' => 'email'])
            ->add('fullName', null, ['label' => 'fullName'])
            ->add(
                'enabled',
                null,
                [
                    'editable' => true,
                    'label' => 'enabled'
                ]
            )
            ->add('lastLogin', null, ['label' => 'last_login'])
            ->add('roles', null, ['label' => 'roles'])
            ->add(
                'createdAt',
                null,
                [
                    'format' => 'm/d/Y',
                    'label' => 'created_at'
                ]
            )
            ->add(
                'updatedAt',
                null,
                [
                    'format' => 'm/d/Y',
                    'label' => 'updated_at'
                ]
            )
            ->add(
                '_action',
                'actions',
                [
                    'actions' => [
                        'edit' => [],
                        'delete' => [],
                        'activateUser' => [
                            'template' => 'AraneumUserBundle:AdminAction:activateUser.html.twig'
                        ],
                        'recoveryPassword' => [
                            'template' => 'AraneumUserBundle:AdminAction:recoveryPassword.html.twig'
                        ],
                    ],
                    'label' => 'actions'
                ]
            );
    }
}