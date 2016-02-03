<?php

namespace Araneum\Bundle\MainBundle\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckRunnersCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckRunnersCommand extends DaemonizedCommand
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:check:runners')
            ->setDescription('Used for demonize. Check All runners.')
            ->addOption('id', null, InputOption::VALUE_OPTIONAL, 'Set runner ID')
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
        $this->getContainer()->get( 'logger' )->info( "Daemon check:runners is running!" );
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        if (empty($this->getInput()->getOption('id'))) {
            $runners = $this->getContainer()
                ->get('doctrine')
                ->getManager()
                ->getRepository('AraneumMainBundle:Connection')
                ->findAll();

            foreach ($runners as $runner) {
                $commandRunner->runSymfonyCommandInNewProcess(
                    'checker:check runner ' . $runner->getId(),
                    $this
                );
            }
        } else {
            $commandRunner->runSymfonyCommandInNewProcess(
                'checker:check runner ' . $this->getInput()->getOption('id'),
                $this
            );
        }
        $this->getDaemon()->iterate( (int)$this->getInput()->getOption('time') );
    }
}
