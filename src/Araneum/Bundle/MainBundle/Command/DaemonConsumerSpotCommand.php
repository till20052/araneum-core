<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Command\AbstractBaseDaemon;

/**
 * Class DaemonConsumerSpotCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class DaemonConsumerSpotCommand extends AbstractBaseDaemon
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:daemon:consumer-spot')
            ->setDescription('Used for demonize. Queue for Spot.')
            ->setHelp('Usage <info>php app/console <name> start|stop|restart|status</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $commandRunner->runSymfonyCommandInNewProcess(
            'rabbitmq:consumer spot --no-debug',
            $this
        );
    }
}
