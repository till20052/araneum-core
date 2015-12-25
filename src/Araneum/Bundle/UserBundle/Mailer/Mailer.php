<?php

namespace Araneum\Bundle\UserBundle\Mailer;

use FOS\UserBundle\Mailer\Mailer as BaseMailer;
use FOS\UserBundle\Model\UserInterface;

/**
 * Class Mailer
 *
 * @package Araneum\Bundle\UserBundle\Mailer
 */
class Mailer extends BaseMailer
{
    /**
     * Send an email to a user to confirm the password reset
     *
     * @param  UserInterface $user
     * @return void
     */
    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = $this->parameters['resetting.template'];
        $url = $this->router->generate('fos_user_resetting_reset', ['token' => $user->getConfirmationToken()], true);
        $rendered = $this->templating->render(
            $template,
            [
                'user' => $user,
                'confirmationUrl' => $url,
            ]
        );
        $this->sendEmailMessage($rendered, $this->parameters['from_email']['resetting'], $user->getEmail());
    }
}
