<?php

namespace Araneum\Bundle\MainBundle\Command\Spot;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;

/**
 * Class GetNewCustomersFromSpot
 *
 * @package Araneum\Bundle\MainBundle\Command\Spot
 */
class GetNewCustomersFromSpotCommand extends DaemonizedCommand
{
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:spot:customers')
            ->setDescription('get Customers.')
            ->setHelp('Usage <info>php app/console <name> <period>application_id</period> <period>time</period> start|stop|restart</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $app = $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findOneById(1);
        $commandRunner = $this->getContainer()->get('araneum.spot.api.customer.service');
        $commandRunner->getAllCustomersByPeriod($app,'P1Y');
    }
}
