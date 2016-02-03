<?php

namespace Araneum\Bundle\MainBundle\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckConnectionsCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckConnectionsCommand extends DaemonizedCommand
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:check:connections')
            ->setDescription('Used for demonize. Check All Connections.')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Set connection ID')
            ->addOption('time', 't', InputOption::VALUE_OPTIONAL, 'Set time deamon run (seconds)', 600)
            ->setHelp('Usage <info>php app/console <name> start|stop|restart</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $this->getContainer()->get( 'logger' )->info( "Daemon check:connections is running!" );
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        if (empty($this->getInput()->getOption('id'))) {
            $connections = $this->getContainer()
                ->get('doctrine')
                ->getManager()
                ->getRepository('AraneumMainBundle:Connection')
                ->findAll();

            foreach ($connections as $connection) {
                $commandRunner->runSymfonyCommandInNewProcess(
                    'checker:check connection ' . $connection->getId(),
                    $this
                );
            }
        } else {
            $commandRunner->runSymfonyCommandInNewProcess(
                'checker:check connection ' . $this->getInput()->getOption('id'),
                $this
            );
        }
        $this->getDaemon()->iterate( (int)$this->getInput()->getOption('time') );
    }
}
