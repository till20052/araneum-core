<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Command\BaseDaemon;
use Symfony\Component\Routing\Exception\InvalidParameterException;

/**
 * Class DaemonCheckerCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class DaemonCheckerCommand extends BaseDaemon
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

        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $em = $this->getContainer()
            ->get('doctrine')
            ->getManager();
        foreach (self::$COMMANDS as $command => $repository) {
            if ($items = $em->getRepository($repository)->findAll()) {
                foreach ($items as $item) {
                     $commandRunner->runSymfonyCommandInNewProcess("checker:check {$command} {$item->getId()}", $this);
                }
            }
        }

        $this->manageTimeIterate();
    }
}
