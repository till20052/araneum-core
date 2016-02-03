<?php

namespace Araneum\Bundle\MainBundle\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckApplicationsCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckApplicationsCommand extends DaemonizedCommand
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:check:applications')
            ->setDescription('Used for demonize. Check All Aplications.')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Set application ID')
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
        $this->getContainer()->get('logger')->info("Daemon check:application is running!");
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        if (empty($this->getInput()->getOption('id'))) {
            $applications = $this->getContainer()
                ->get('doctrine')
                ->getManager()
                ->getRepository('AraneumMainBundle:Application')
                ->findAll();

            foreach ($applications as $app) {
                $commandRunner->runSymfonyCommandInNewProcess(
                    'checker:check application '.$app->getId(),
                    $this
                );
            }
        } else {
            $commandRunner->runSymfonyCommandInNewProcess(
                'checker:check application '.$this->getInput()->getOption('id'),
                $this
            );
        }
        $this->getDaemon()->iterate((int)$this->getInput()->getOption('time'));
    }
}
