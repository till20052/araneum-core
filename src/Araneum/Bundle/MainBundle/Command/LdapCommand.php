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
    const DEFAULT_ROLE = 'ROLE_ADMIN';

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
            ->setDescription('Load all users from LDAP server.')
            ->addOption(
                'group',
                'g',
                InputOption::VALUE_OPTIONAL
            )
            ->addOption(
                'role',
                'r',
                InputOption::VALUE_OPTIONAL
            );
    }

    /**
     * Execute command
     *
     * @inheritdoc
     * @param OutputInterface $output
     * @param InputInterface $input
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $filterGroup = $input->getOption('group');
        $addRole = ($input->getOption('role'))?$input->getOption('role'):self::DEFAULT_ROLE;

        $apiLdap = $this->getContainer()
            ->get('api.ldap.synchronization');
        $apiLdap->setLdapParameter(['default_user_roles' => $addRole]);
        if (!empty($filterGroup)) {
            $apiLdap->setFilterQuery(['cn' => $filterGroup]);
        }
        $completed = $apiLdap->runSynchronization();
        $this->output->writeln("User: Save({$completed['sitem']})/Update({$completed['uitem']}).");
        $this->output->writeln('Success');
    }
}
