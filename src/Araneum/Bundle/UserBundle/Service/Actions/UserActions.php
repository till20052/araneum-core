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
                        'label' => 'users.DELETE_USER',
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
                        'label' => 'users.DISABLE_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
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
                        'label' => 'users.ENABLE_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'create',
                [
                    'form' => 'araneum_user_admin_user_get',
                    'callback' => 'create',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'users.CREATE_NEW',
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
                        'label' => 'users.EDIT_USER',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            );
//            ->add(
//            'deleteGroup',
//            [
//                'resource' => 'araneum_root', //example!!! plz change in AC-228
//                'callback' => 'deleteRow',
//                'confirm' => [
//                    'title' => 'Are you sure?',
//                    'yes' => [
//                        'class' => 'confirm',
//                        'title' => 'Yes, delete it!',
//                    ],
//                    'no' => [
//                        'class' => 'cancel',
//                        'title' => 'Cancel',
//                    ],
//                ],
//                'display' => [
//                    'btnClass' => 'btn-danger',
//                    'icon' => 'icon-user-unfollow',
//                    'label' => 'Delete user',
//                ],
//                'position' => ActionBuilderInterface::POSITION_ALL,
//            ]
//        );
    }
}
