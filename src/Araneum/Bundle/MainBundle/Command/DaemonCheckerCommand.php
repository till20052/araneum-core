<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Exception\InvalidFormException;
use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Class DaemonCheckerCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class DaemonCheckerCommand extends DaemonizedCommand
{
    protected static $COMMANDS = [
        'connection'    => 'AraneumMainBundle:Connection',
        'cluster'       => 'AraneumMainBundle:Cluster',
        'application'   => 'AraneumMainBundle:Application',
        'runner'        => 'AraneumMainBundle:Runner',
    ];

    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:daemon:checker')
            ->setDescription('Used for demonize. Checker check.')
            ->setHelp('Usage <info>php app/console <name> start|stop|restart</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $this->getContainer()->get('logger')->info("Daemon araneum:daemon:checker is running!");
        $daemonInterval = $this->getContainer()->getParameter('araneum_daemon_checker_iterate');
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $em = $this->getContainer()
            ->get('doctrine')
            ->getManager();
        $daemonInterval = strtotime($daemonInterval) - time();
        if (empty($daemonInterval) || $daemonInterval < 0) {
            throw new InvalidParameterException('Interval daemon incorrect format (use: year, month, week, day, hours, minutes, seconds).');
        }

        foreach (self::$COMMANDS as $command => $repository) {
            if ($items = $em->getRepository($repository)->findAll()) {
                foreach ($items as $item) {
                     $commandRunner->runSymfonyCommandInNewProcess("checker:check {$command} {$item->getId()}", $this);
                }
            }
        }

        $this->getDaemon()
            ->iterate($daemonInterval);
    }
}
