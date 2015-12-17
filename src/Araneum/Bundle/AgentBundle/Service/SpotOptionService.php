<?php

namespace Araneum\Bundle\AgentBundle\Service;

/**
 * Class SpotOptionService
 *
 * @package Araneum\Bundle\AgentBundle\Service
 */
class SpotOptionService
{
    /**
     * SpotOption Login
     *
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function login($login, $password)
    {
        $login = null;
        $password = null;

        return true;
    }

    /**
     * Reset Customer Password on SpotOption
     *
     * @param string $login
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function resetPassword($login, $currentPassword, $newPassword)
    {
        $login = null;
        $currentPassword = null;
        $newPassword = null;

        return true;
    }
}
