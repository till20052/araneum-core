<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Acl\Exception\Exception;

/**
 * Class SpotCustomersCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class SpotCustomersCommand extends ContainerAwareCommand
{

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('araneum:spot:get-customers')
            ->setDescription('Send all new customers to apps by api url')
            ->addArgument(
                'period',
                InputArgument::OPTIONAL,
                'Period to get data from Spot',
                'P1Y'
            )
            ->addArgument(
                'project',
                InputArgument::IS_ARRAY,
                'Array of application names',
                []
            )
        ;
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @inheritdoc
     * @return null;
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = $this->getContainer()->get('logger');
        $periodOption = $input->getArgument('period');
        $applicationOption = $input->getArgument('project');
        if (empty($applicationOption)) {
            $applications =  $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findAll();
        } else {
            $applications =  $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findByName($applicationOption);
        }
        $this->getContainer()->get('doctrine')->resetManager();
        foreach ($applications as $application) {
            try {
                $credential = $application->getSpotCredential();

                foreach ($credential as $value) {
                    if (empty($value)) {
                        throw new Exception('Cannot get Spot credential of application '.$application->getName());
                    }
                }
                $spotCustomerService = $this->getContainer()->get('araneum.spot.api.customer.service');
                $data = $spotCustomerService->getAllCustomersByPeriod($application, $periodOption);

                if (empty($data)) {
                    throw new Exception('Empty data from Spot in application '.$application->getName());
                }

                $data = json_decode($data, true);
                $result = $data['status'];
                $emails = [];

                if (isset($result['errors']) && !empty($result['errors'])) {
                    throw new Exception('Errors while getting data from Spot. Application : '.$application->getName());
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
                    $output->writeln('All users are actual in application '.$application->getName());
                }

            } catch (\Exception $e) {
                $log->addError($e->getMessage());
                $output->writeln($e->getMessage());
            }
        }
        $output->writeln('Finishing Command');
    }
}
