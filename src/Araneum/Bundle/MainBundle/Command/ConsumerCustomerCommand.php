<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Command\AbstractBaseDaemon;

/**
 * Class DeploymentCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class ConsumerCustomerCommand extends AbstractBaseDaemon
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:consumer:customer')
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
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $commandRunner->runSymfonyCommandInNewProcess(
            'rabbitmq:consumer spot_customer --no-debug',
            $this
        );
    }
}
