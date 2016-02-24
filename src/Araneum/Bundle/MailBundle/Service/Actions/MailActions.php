<?php

namespace Araneum\Bundle\MailBundle\Service\Actions;

use Araneum\Base\Service\Actions\AbstractActions;
use Araneum\Base\Service\Actions\ActionBuilderInterface;

/**
 * Class MailActions
 *
 * @package Araneum\Bundle\MailBundle\Service\Actions
 */
class MailActions extends AbstractActions
{
    /**
     * Build mail actions
     *
     * @param ActionBuilderInterface $builder
     */
    public function buildActions(ActionBuilderInterface $builder)
    {
        $builder
            ->add(
                'delete',
                [
                    'resource' => 'araneum_mail_admin_mail_delete',
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
                        'label' => 'mails.DELETE_MAIL',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ALL,
                ]
            )
            ->add(
                'show',
                [
                    'info' => 'araneum_mail_admin_mail_get',
                    'display' => [
                        'btnClass' => 'btn-success',
                        'icon' => 'icon-user-follow',
                        'label' => 'mails.SHOW_MAIL',
                    ],
                    'position' => ActionBuilderInterface::POSITION_ROW,
                ]
            );
    }
}
