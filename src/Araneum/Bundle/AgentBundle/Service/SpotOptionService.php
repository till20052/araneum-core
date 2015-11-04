<?php

namespace Araneum\Bundle\AgentBundle\Service;

class SpotOptionService
{
    /**
     * SpotOption Login
     *
     * @param $login
     * @param $password
     * @return bool
     */
    public function login($login, $password)
    {
        return true;
    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param $login
     * @param $currentPassword
     * @param $newPassword
     * @return bool
     */
    public function resetPassword($login, $currentPassword, $newPassword)
    {
        return true;
    }
}