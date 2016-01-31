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
        $senderService = $this->getContainer()->get('araneum.main.application.api_handler');
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        $output->writeln('All is done perfectly!!!');
    }
}
