<?php

namespace Araneum\Base\MikSoftware\DaemonBundle\Daemon;

use \MikSoftware\DaemonBundle\Daemon\Daemon as BaseDaemon;

/**
 * Class Daemon
 *
 * @package Araneum\Base\MikSoftware\DaemonBundle\Daemon
 */
class Daemon extends BaseDaemon
{
    protected function changeIdentity($gid = 0, $uid = 0)
    {
        // Change identity. maybe
        if (posix_geteuid() === 0) {
            return parent::changeIdentity($gid, $uid);
        }

        return false;
    }
}
