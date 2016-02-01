<?php

namespace Araneum\Bundle\MainBundle\Command\Applications;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
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
            ->setDescription('Send all new customers to apps by api url');
    }

    /**
     * Execute command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return null;
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $optionService = $this->getContainer()->get('araneum.api.application.option.service');
        $em = $this->getContainer()->get('doctrine')->getManager();
        $applications = $em->getRepository('AraneumMainBundle:Application')->findAll();
        foreach ($applications as $application) {
            $customers = $em->getRepository("AraneumAgentBundle:Customer")->findBy(
                ['enableSite'=>false, 'application'=> $application]
            );
            $output->writeln(count($customers));

            //TODO Настроить на стороне application и в зависимости от этого изменить url
            $answer = $optionService->sendCustomersToApplication(
                $customers,
                $application,
                '/api/customers/new'
            );

            $output->writeln($answer);
        }
        $output->writeln('Finishing Command');
    }
}
