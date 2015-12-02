<?php

namespace Araneum\Bundle\MainBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

class LocaleActions extends AbstractActions
{
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder->add(
            'deleteGroup',
            [
                'resource' => 'deleteLocaleActionRoute', //example!!! plz change in AC-228
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
        );
    }
}