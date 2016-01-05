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
                'delete',
                [
                    'resource' => 'araneum_main_admin_locale_delete',
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
                        'label' => 'locales.DELETE_LOCALE',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'enabledDisabled',
                [
                    'resource' => 'araneum_main_admin_locale_disable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'locales.DISABLE_LOCALE',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'enabledDisabled',
                [
                    'resource' => 'araneum_main_admin_locale_enable',
                    'callback' => 'editRow',
                    'display' => [
                        'btnClass' => 'btn btn-sm btn-default',
                        'icon' => 'icon-lock-open',
                        'label' => 'locales.ENABLE_LOCALE',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'create',
                [
                    'form' => 'araneum_admin_main_locale_get',
                    'callback' => 'create',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'locales.CREATE_NEW',
                    ],
                    'position' => ActionBuilderInterface::POSITION_TOP,
                ]
            )
            ->add(
                'update',
                [
                    'form' => 'araneum_admin_main_locale_get',
                    'callback' => 'update',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'locales.EDIT_LOCALE',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            );
    }
}
