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
            $this->getOutput()->writeln('Daemon '.$this->getName().' right now is running');

            return true;
        }
        $this->getOutput()->writeln('Daemon '.$this->getName().' doesn\'t work');

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
