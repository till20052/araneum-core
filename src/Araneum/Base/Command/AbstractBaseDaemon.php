<?php
namespace Araneum\Base\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Demonized Symfony Base command using in project
 */
abstract class AbstractBaseDaemon extends DaemonizedCommand
{
    const BASE_METHODS = ['status'];

    const DAEMON_STATUS = [
        'up'   => 'Daemon is setting up',
        'down' => 'Daemon doesn\'t work',
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->addMethods(self::BASE_METHODS);
    }

    /**
     * {@inheritDoc}
     */
    protected function status()
    {
        if ($this->getDaemon()->isRunning()) {
            $this->getOutput()->writeln(self::DAEMON_STATUS['up']);

            return true;
        }
        $this->getOutput()->writeln(self::DAEMON_STATUS['down']);

        return false;
    }

    /**
     * Making time interval using in daemon, or throwing Exception
     * @return mixed
     */
    protected function manageTimeIterate()
    {
        $intervals = $this->getContainer()
            ->getParameter('daemons_iterate');
        $timeInterval = $intervals[preg_replace('/:/', '_', $this->getName())];
        if (isset($timeInterval) && !empty($timeInterval)) {
            $time = strtotime($timeInterval) - time();
            if (empty($time) || $time < 0) {
                throw new InvalidParameterException('Interval daemon incorrect format (use: year, month, week, day, hours, minutes, seconds).');
            }
            $this->getDaemon()
                ->iterate($time);
        }
    }
}
