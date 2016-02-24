<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Araneum\Base\Command\AbstractBaseDaemon;

/**
 * Class CheckerDaemonsCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckDaemonsCommand extends ContainerAwareCommand
{
    const BROKEN_DAEMONS = [];

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('araneum:check:daemons')
            ->setDescription('Check cluster or application state status.');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $daemonsArray = [];
            $daemonsToStart = [];
            $this->output = $output;
            $configDaemonsArray = $this->getContainer()->getParameter('mik_software_daemon.daemons');

            foreach (array_keys($configDaemonsArray) as $name) {
                array_push($daemonsArray, preg_replace('/_/', ':', $name));
            }

            foreach ($daemonsArray as $daemon) {
                if (!in_array($daemon, self::BROKEN_DAEMONS)) {
                    $process = new Process('app/console '.$daemon.' status');
                    $process->run(function ($err, $data) use (&$daemon, &$daemonsToStart) {
                        if (Process::ERR === $err) {
                            $this->output->writeln('Cannot get daemon status: '.$daemon);
                        } elseif (trim($data) == AbstractBaseDaemon::DAEMON_STATUS['down']) {
                            array_push($daemonsToStart, $daemon) ;
                            $this->output->writeln($daemon.' was broken');
                        } elseif (trim($data) == AbstractBaseDaemon::DAEMON_STATUS['up']) {
                            $this->output->writeln($daemon.' is up. Doesn\'t need to restart');
                        }
                    });
                }
            }

            if (!empty($daemonsToStart)) {
                $this->output->writeln('Restarting broken daemons: '.implode(' , ', $daemonsToStart));
                $command = 'app/console '.implode(' start && app/console ', $daemonsToStart).' start';
                (new Process($command))->run();
            } else {
                $this->output->writeln('There are no daemons to restart!');
            }

        } catch (ProcessTimedOutException $e) {
            $this->output->writeln('All daemons successfully checked and sated up');
        }
    }
}
