<?php

namespace Araneum\Bundle\MainBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

class LocaleActions extends AbstractActions
{
    /**
     * Build locale actions
     *
     * @param ActionBuilderInterface $builder
     */
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder->add(
            'deleteGroup',
            [
                'resource' => '/manage/admin/locale/delete',
                'callback' => 'deleteRow',
                'confirm' => [
                    'title' => 'Are you sure?',
                    'yes' => [
                        'class' => 'confirm',
                        'title' => 'Yes, delete it!'
                    ],
                    'no' => [
                        'class' => 'cancel',
                        'title' => 'Cancel'
                    ]
                ],
                'display' => [
                    'btnClass' => 'btn-danger',
                    'icon' => 'icon-user-unfollow',
                    'label' => 'Delete locales',
                ],
                'position' => ActionBuilderInterface::POSITION_ALL,
            ]
        )
            ->add(
                'delete',
                [
                    'resource' => '/manage/admin/locale/delete',
                    'callback' => 'deleteRow',
                    'confirm' => [
                        'title' => 'Are you sure?',
                        'yes' => [
                            'class' => 'confirm',
                            'title' => 'Yes, delete it!'
                        ],
                        'no' => [
                            'class' => 'cancel',
                            'title' => 'Cancel'
                        ]
                    ],
                    'display' => [
                        'btnClass' => 'btn-danger',
                        'icon' => 'icon-user-unfollow',
                        'label' => 'Delete locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            )
            ->add(
                'disableGroup',
                [
                    'resource' => '/manage/admin/locale/disable',
                    'callback' => 'disableGroup',
                    'confirm' => [
                        'title' => 'Are you sure?',
                        'yes' => [
                            'class' => 'confirm',
                            'title' => 'Yes, delete it!'
                        ],
                        'no' => [
                            'class' => 'cancel',
                            'title' => 'Cancel'
                        ]
                    ],
                    'display' => [
                        'btnClass' => 'btn-danger',
                        'icon' => 'icon-user-unfollow',
                        'label' => 'Disable locales',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'enableGroup',
                [
                    'resource' => '/manage/admin/locale/enable',
                    'callback' => 'enableGroup',
                    'confirm' => [
                        'title' => 'Are you sure?',
                        'yes' => [
                            'class' => 'confirm',
                            'title' => 'Yes, delete it!'
                        ],
                        'no' => [
                            'class' => 'cancel',
                            'title' => 'Cancel'
                        ]
                    ],
                    'display' => [
                        'btnClass' => 'icon-lock-open',
                        'icon' => 'icon-lock-open',
                        'label' => 'Enable locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'Disable',
                [
                    'resource' => '/manage/admin/locale/disable',
                    'callback' => 'disable',
                    'display' => [
                        'btnClass' => 'btn-danger',
                        'icon' => 'icon-lock-close',
                        'label' => 'Disable locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            )
            ->add(
                'Enable',
                [
                    'resource' => '/manage/admin/locale/enable',
                    'callback' => 'enable',
                    'display' => [
                        'btnClass' => 'btn-danger',
                        'icon' => 'icon-lock-open',
                        'label' => 'Enable locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            )
        ->add(
            'Create',
            [
                'resourse'  => '/manage/admin/locale/create',
                'callback'  => 'create',
                'display'   =>[
                    'btnClass' => 'btn-success',
                    'icon' => 'fa-plus',
                    'label' => 'Create',
                ],
                'position' => ActionBuilderInterface::POSITION_ALL,
            ]
        );

    }
}