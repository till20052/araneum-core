<?php

namespace Araneum\Bundle\UserBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

/**
 * Class UserActions
 *
 * @package Araneum\Bundle\UserBundle\Service\Actions
 */
class UserActions extends AbstractActions
{
    /**
     * Build users actions
     *
     * @param ActionBuilderInterface $builder
     */
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder
            ->add(
                'create',
                [
                    'form' => 'araneum_user_admin_user_get',
                    'callback' => 'create',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'user.data_grid.CREATE_NEW',
                    ],
                    'position' => ActionBuilderInterface::POSITION_TOP,
                ]
            )
            ->add(
                'update',
                [
                    'form' => 'araneum_user_admin_user_get',
                    'callback' => 'update',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'user.data_grid.EDIT_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            )
            ->add(
                'enabledDisabled',
                [
                    'resource' => 'araneum_user_admin_user_enable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'user.data_grid.ENABLE_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'enabledDisabled',
                [
                    'resource' => 'araneum_user_admin_user_disable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'user.data_grid.DISABLE_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'delete',
                [
                    'resource' => 'araneum_user_admin_user_delete',
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
                        'label' => 'user.data_grid.DELETE_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'ldap',
                [
                    'resourceAll' => 'araneum_user_admin_users_ldap_sync',
                    'callback' => 'refresh',
                    'confirm' => [
                        'title' => 'admin.general.SURE',
                        'yes' => [
                            'class' => 'confirm',
                            'title' => 'admin.general.CONFIRM_LDAP_SYNC',
                        ],
                        'no' => [
                            'class' => 'cancel',
                            'title' => 'admin.general.CANCEL',
                        ],
                    ],
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-refresh',
                        'label' => 'user.data_grid.LDAP_USERS',
                    ],
                    'position' => ActionBuilderInterface::POSITION_TOP,
                ]
            );
    }
}
