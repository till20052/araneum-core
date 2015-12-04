<?php

namespace Araneum\Bundle\UserBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

class UserActions extends AbstractActions
{
    /**
     * Build users actions
     *
     * @param ActionBuilderInterface $builder
     */
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder->add(
            'deleteGroup',
            [
                'resource' => 'araneum_root', //example!!! plz change in AC-228
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
                    'label' => 'Delete user',
                ],
                'position' => ActionBuilderInterface::POSITION_ALL,
            ]
        );
    }
}