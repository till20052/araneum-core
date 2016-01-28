<?php

namespace Araneum\Bundle\MainBundle\Command\Spot;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Filesystem\Filesystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
/**
 * Class GetNewCustomersFromSpot
 *
 * @package Araneum\Bundle\MainBundle\Command\Spot
 */
class GetNewCustomersFromSpotCommand extends DaemonizedCommand
{

    const DEFAULT_PERIOD = 'P1Y';
    const APPLICATION_ID = 1;
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:spot:customers')
            ->setDescription('get Customers.')
            ->addOption(
                'application',
                '-a',
                InputOption::VALUE_REQUIRED,
                'Application to execute',
                self::APPLICATION_ID
            )
            ->addOption(
                'period',
                '-p',
                InputOption::VALUE_REQUIRED,
                'Period of customers registration',
                self::DEFAULT_PERIOD
            )
            ->setHelp('Usage <info>php app/console <name> <period>application_id</period> <period>time</period> start|stop|restart</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $application = $this->input->getOption('application');
        $period = $this->input->getOption('period');
        $app = $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findOneById($application);
        $commandRunner = $this->getContainer()->get('araneum.spot.api.customer.service');
        $commandRunner->getAllCustomersByPeriod($app, $period);
        $this->getContainer()->get('logger')->info('Daemon is running!');
        $this->daemon->iterate(10);
    }
}
