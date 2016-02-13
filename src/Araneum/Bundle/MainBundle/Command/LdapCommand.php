<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Bundle\UserBundle\Entity\User;
use Araneum\Bundle\UserBundle\Entity\UserLdapLog;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LdapCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class LdapCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('araneum:ldap:users')
            ->setDescription('Load all users in LDAP.');
    }

    /**
     * Execute command
     *
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(OutputInterface $output)
    {
        $this->output = $output;
        $completed = $this->getContainer()
            ->get('api.ldap.synchronization')
            ->runSynchronization();
        $this->output->writeln("User: Save({$completed['sitem']})/Update({$completed['uitem']}).");
        $this->output->writeln('Success');
    }
}
