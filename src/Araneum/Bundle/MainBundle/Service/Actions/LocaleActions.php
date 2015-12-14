<?php

namespace Araneum\Bundle\MainBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

/**
 * Class LocaleActions
 *
 * @package Araneum\Bundle\MainBundle\Service\Actions
 */
class LocaleActions extends AbstractActions
{
    /**
     * Build locale actions
     *
     * @param ActionBuilderInterface $builder
     */
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder
            ->add(
                'locales.DELETE_LOCALE',
                [
                    'resource' => 'araneum_main_admin_locale_delete',
                    'callback' => 'deleteRow',
                    'confirm' => [
                        'title' => 'Are you sure?',
                        'yes' => [
                            'class' => 'confirm',
                            'title' => 'Yes, delete it!',
                        ],
                        'no' => [
                            'class' => 'cancel',
                            'title' => 'Cancel',
                        ],
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
                'locales.DISABLE_LOCALE',
                [
                    'resource' => 'araneum_main_admin_locale_disable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'Disable locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'locales.ENABLE_LOCALE',
                [
                    'resource' => 'araneum_main_admin_locale_enable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'Enable locale',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'locales.CREATE_NEW',
                [
                    'resourse' => 'araneum_main_admin_locale_create',
                    'callback' => 'create',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'Create',
                    ],
                    'position' => ActionBuilderInterface::POSITION_TOP,
                ]
            );
    }
}
