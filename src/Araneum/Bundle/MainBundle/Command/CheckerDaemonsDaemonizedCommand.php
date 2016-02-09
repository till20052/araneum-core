<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Command\BaseDaemon;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class CheckerDaemonsCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckerDaemonsDaemonizedCommand extends BaseDaemon
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('daemon:check:daemons')
            ->setDescription('Used for demonize. Queue for send Customer to Spot.')
            ->setHelp('Usage <info>php app/console <name> start|stop|restart|status</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $interval = $this->getContainer()->getParameter('daemon_check_daemons_interval');
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $commandRunner->runSymfonyCommandInNewProcess(
            'check:daemons',
            $this
        );
    }
}
