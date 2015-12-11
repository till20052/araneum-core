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
        return true;
    }
}
