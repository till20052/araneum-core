<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Command\AbstractBaseDaemon;

/**
 * Class DaemonCheckerCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class DaemonCheckSiteCommand extends AbstractBaseDaemon
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
            ->setName('araneum:daemon:check-site')
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

        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $em = $this->getContainer()
            ->get('doctrine')
            ->getManager();
        foreach (self::$COMMANDS as $command => $repository) {
            if ($items = $em->getRepository($repository)->findAll()) {
                foreach ($items as $item) {
                     $commandRunner->runSymfonyCommandInNewProcess("araneum:check:site {$command} {$item->getId()}", $this);
                }
            }
        }

        $this->manageTimeIterate();
    }
}
