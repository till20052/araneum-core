<?php
/**
 * Created by PhpStorm.
 * User: andreyp
 * Date: 18.09.15
 * Time: 17:29
 */

namespace Araneum\Bundle\MainBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class ClusterAdmin extends Admin
{
    /**
     * Fields to be shown on create/edit forms
     *
     * @params FormMapper
     * */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('name', 'text', array('label' => 'Name'))
            ->add('host', 'entity', array('class' => 'Araneum\Bundle\MainBundle\Entity\Connection', 'label'=>'Host'))
            ->add('type', 'text', array('label'=>'Type'))
            ->add('status', 'choice', array('choices'=>array(1=>'status1', 2=>'status2'), 'label'=>'Status'))
            ->add('enabled', 'checkbox', array('label'=>'Enabled'))
            ->add('created_at','text' );
    }

    /**
     * Fields to be shown on the filter panel
     *
     * @params DataGridMapper
     * */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('name', null, array('label' => 'Name'))
            ->add('host', null, array('label' => 'Host'))
            ->add('type', null, array('label' => 'Type'))
            ->add('status', null, array('label' => 'Status'))
            ->add('enabled', null, array('label' => 'Enabled'))
            ->add('createdAt', 'doctrine_orm_date', array('input_type' => 'timestamp', 'label' => 'Created at'))
        ;
    }

    /**
     * Fields to be shown on list
     *
     * @params ListMapper
     * */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('name')
            ->add('host')
            ->add('type')
            ->add('status')
            ->add('enabled')
            ->add('created_at')
            ->add('_action', 'actions', array(
                'actions'=>array(
                    'show'=>array(),
                    'edit'=>array()
                )
            ));
    }
}