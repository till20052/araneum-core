<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class SendCustomersCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class SendCustomersCommand extends ContainerAwareCommand
{

    /**
     * Configure command
     */
    protected function configure()
    {
        $this
            ->setName('apps:send:customers')
            ->setDescription('Send all new customers to apps by api url')
            ->addArgument(
                'app',
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
        $optionService = $this->getContainer()->get('araneum.api.application.service');
        $applicationOption = $input->getArgument('app');
        $em = $this->getContainer()->get('doctrine')->getManager();
        if (empty($applicationOption)) {
            $applications =  $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findAll();
        } else {
            $applications =  $this->getContainer()->get('doctrine')->getRepository('AraneumMainBundle:Application')->findByName($applicationOption);
        }
        foreach ($applications as $application) {
            $customers = $em->getRepository("AraneumAgentBundle:Customer")->findBy(
                ['siteId' => null, 'application' => $application]
            );
            $output->writeln(count($customers));
            foreach ($customers as $customer) {
                $optionService->createCustomer(
                    $customer,
                    $application
                );
            }
        }
        $output->writeln('Finishing Command');
    }
}
