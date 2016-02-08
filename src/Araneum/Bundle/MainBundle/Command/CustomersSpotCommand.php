<?php

namespace Araneum\Bundle\MainBundle\Command;

use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class GetNewCustomersFromSpot
 * php app/console araneum:spot:customers stop
 *
 * @package Araneum\Bundle\MainBundle\Command\Spot
 */
class CustomersSpotCommand extends DaemonizedCommand
{

    const DEFAULT_PERIOD = 'P1Y';
    const APPLICATION_ID = 1;

    private $em;
    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {   
        $this
            ->setName('araneum:spot:customers')
            ->setDescription('get Customers.')
            ->addOption(
                'period',
                '-p',
                InputOption::VALUE_REQUIRED,
                'Period of customers registration',
                self::DEFAULT_PERIOD
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

        $period = $this->input->getOption('period');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $applications = $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findAll();
        foreach ($applications as $application) {
            try {
                $credential = $application->getSpotCredential();

                foreach ($credential as $value) {
                    if (empty($value)) {
                        throw new Exception('Cannot get Spot credential of application '.$application->getName());
                    }
                }
                $spotCustomerService = $this->getContainer()->get('araneum.spot.api.customer.service');
                $data = $spotCustomerService->getAllCustomersByPeriod($application, $period);

                if (empty($data)) {
                    throw new Exception('Empty data from Spot in application '.$application->getName());
                }

                $data = json_decode($data, true);
                $result = $data['status'];
                $emails = [];

                if (isset($result['errors']) && !empty($result['errors'])) {
                    throw new Exception('Errors while getting data from Spot: '.$result['errors']);
                }

                foreach ($result['Customer'] as $customer) {
                    array_push($emails, $customer['email']);
                }

                $existingEmails = $spotCustomerService->getExistCustomerEmails($emails, $application);

                if (!empty($existingEmails)) {
                    foreach ($result['Customer'] as $customer) {
                        if (in_array($customer['email'], $existingEmails)) {
                            $spotCustomerService->addSpotCustomer($customer, $application);
                        }
                    }
                } else {
                    throw new Exception('All users are actual in application '.$application->getName());
                }

            } catch (\Exception $e) {
                $this->log($e->getMessage());
                $this->output->writeln($e->getMessage());
            }
        }
        $this->em->flush();
        $this->getDaemon()->iterate($daemonInterval);
    }
}
