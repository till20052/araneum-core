<?php

namespace Araneum\Bundle\MainBundle\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckClustersCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckClustersCommand extends DaemonizedCommand
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:check:clusters')
            ->setDescription('Used for demonize. Check All Clusters.')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Set cluster ID')
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
        $this->getContainer()->get('logger')->info("Daemon check:clusters is running!");
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        if (empty($this->getInput()->getOption('id'))) {
            $clusters = $this->getContainer()
                ->get('doctrine')
                ->getManager()
                ->getRepository('AraneumMainBundle:Cluster')
                ->findAll();

            foreach ($clusters as $cluster) {
                $commandRunner->runSymfonyCommandInNewProcess(
                    'checker:check cluster '.$cluster->getId(),
                    $this
                );
            }
        } else {
            $commandRunner->runSymfonyCommandInNewProcess(
                'checker:check cluster '.$this->getInput()->getOption('id'),
                $this
            );
        }
        $this->getDaemon()->iterate((int)$this->getInput()->getOption('time'));
    }
}
