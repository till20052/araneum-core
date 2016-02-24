<?php

namespace Araneum\Bundle\MainBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

/**
 * Class ClusterActions
 *
 * @package Araneum\Bundle\MainBundle\Service\Actions
 */
class ClusterActions extends AbstractActions
{
    /**
     * Build cluster actions
     *
     * @param ActionBuilderInterface $builder
     */
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder
            ->add(
                'delete',
                [
                    'resource' => 'araneum_main_admin_cluster_delete',
                    'callback' => 'deleteRow',
                    'confirm' => [
                        'title' => 'admin.general.SURE',
                        'yes' => [
                            'class' => 'confirm',
                            'title' => 'admin.general.CONFIRM_DELETE',
                        ],
                        'no' => [
                            'class' => 'cancel',
                            'title' => 'admin.general.CANCEL',
                        ],
                    ],
                    'display' => [
                        'btnClass' => 'btn-danger',
                        'icon' => 'icon-user-unfollow',
                        'label' => 'clusters.DELETE_CLUSTER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'enabledDisabled',
                [
                    'resource' => 'araneum_main_admin_cluster_disable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'clusters.DISABLE_CLUSTER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'enabledDisabled',
                [
                    'resource' => 'araneum_main_admin_cluster_enable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'clusters.ENABLE_CLUSTER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'create',
                [
                    'form' => 'araneum_admin_main_cluster_get',
                    'callback' => 'create',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'clusters.CREATE_NEW',
                    ],
                    'position' => ActionBuilderInterface::POSITION_TOP,
                ]
            )
            ->add(
                'update',
                [
                    'form' => 'araneum_admin_main_cluster_get',
                    'callback' => 'update',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'clusters.EDIT_CLUSTER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            )
            ->add(
                'checkStatus',
                [
                    'resource' => 'araneum_main_admin_cluster_status',
                    'callback' => 'update',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-refresh',
                        'label' => 'clusters.CHECK_STATUS',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            );
    }
}
