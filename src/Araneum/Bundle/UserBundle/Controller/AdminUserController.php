<?php

namespace Araneum\Bundle\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Araneum\Bundle\UserBundle\Entity\User;

class AdminUserController extends Controller
{
    public function activateUserAction()
    {
        // TODO: add you implementation here
    }

    public function recoverPasswordAction()
    {
        // TODO: add you implementation here
    }

    /**
     * Get user settings
     *
     * @Route("/get_settings/", name="araneum_user_get_settings")
     * @return Response
     */
    public function getSettingsAction()
    {
        $user = $this->getUser();

        return new Response($user->getSettings());
    }

    /**
     * Set
     *
     * @param $settings
     */
    public function setSettingsAction(Request $settings)
    {
        $user = $this->getUser();
    }
}