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
                    'label' => 'Delete locale',
                ],
                'position' => ActionBuilderInterface::POSITION_ALL,
            ]
        )
            ->add(
                'lockGroup',
                [
                    'resource' => '/manage/admin/locale/disable',
                    'callback' => 'lockGroup',
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
                        'label' => 'Lock locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'unlockGroup',
                [
                    'resource' => '/manage/admin/locale/enable',
                    'callback' => 'unlockGroup',
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
                        'label' => 'unLock locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'lock',
                [
                    'resource' => '/manage/admin/locale/disable',
                    'callback' => 'lock',
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
                        'label' => 'Lock locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            )
            ->add(
                'unlockGroup',
                [
                    'resource' => '/manage/admin/locale/enable',
                    'callback' => 'unlockGroup',
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
                        'label' => 'unLock locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            );

    }
}