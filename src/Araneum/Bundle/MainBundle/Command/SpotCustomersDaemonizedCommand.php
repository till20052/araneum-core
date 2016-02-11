<?php

namespace Araneum\Bundle\MainBundle\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class SpotCustomersDaemonizedCommand
 * php app/console araneum:spot:customers stop
 *
 * @package Araneum\Bundle\MainBundle\Command\Spot
 */
class SpotCustomersDaemonizedCommand extends DaemonizedCommand
{

    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:daemon:get-customers')
            ->setDescription('Daemonized araneum:spot:get-customers command')
            ->addOption(
                'project',
                '-p',
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Application|Applications domain to work with',
                []
            )
            ->addOption(
                'period',
                '-t',
                InputOption::VALUE_REQUIRED,
                'Period of customers registration',
                'P1Y'
            )
            ->setHelp('Usage <infoaraneumDB>php app/console <name> <period>application_id</period> <period>time</period> start|stop|restart</infoaraneumDB>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $daemonInterval = $this->getContainer()
            ->getParameter('spot_customer_daemon_timeout');
        $periodOption = $this->input->getOption('period');
        $applicationOption = $this->input->getOption('project');

        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        try {
            $appString = implode(' ', $applicationOption);
            $commandRunner->runSymfonyCommandInNewProcess(
                'araneum:spot:get-customers '.$periodOption.' '.$appString
            );
            if (empty($appString)) {
                $appString = 'All applications';
            }
            $this->output->writeln('All customer data synchronised in applications: '.$appString.' By period: '.$periodOption);
        } catch (Exception $e) {
            $this->log($e->getMessage());
            $this->output->writeln($e->getMessage());
        }
        $this->getDaemon()->iterate($daemonInterval);
    }
}
