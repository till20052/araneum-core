<?php
namespace Araneum\Base\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;

abstract class BaseDaemon extends DaemonizedCommand
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

    protected function status()
    {
        if ($this->getDaemon()->isRunning()) {
            $this->getOutput()->writeln('true');

            return true;
        }

        $this->getOutput()->writeln('false');
        return false;
    }
}