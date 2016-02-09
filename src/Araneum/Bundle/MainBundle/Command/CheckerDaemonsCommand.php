<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class CheckerDaemonsCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckerDaemonsCommand extends ContainerAwareCommand
{
    const BROKEN_DAEMONS = [
        'daemon:check:daemons',
    ];

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('check:daemons')
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
        $this->output = $output;
        $configDaemonsArray = $this->getContainer()->getParameter('mik_software_daemon.daemons');
        $daemonsArray = array();
        foreach (array_keys($configDaemonsArray) as $name) {
            array_push($daemonsArray, preg_replace('/_/', ':', $name));
        }

        $startDaemons = '';
        foreach ($daemonsArray as $index => $daemon) {
            if (!in_array($daemon, self::BROKEN_DAEMONS)) {
                $process = new Process('app/console '.$daemon.' status');
                if (!$process->isRunning()) {
                    $process->run(function ($err, $data) use (&$startDaemons, &$daemon, &$index) {
                        if (Process::ERR === $err) {
                            $this->output->writeln('Cannot get daemon status: '.$daemon);
                        } elseif ($data != 'true') {
                            if ($index != 0) {
                                $startDaemons .= ' && app/console '.$daemon.' start';
                            } else {
                                $startDaemons .= 'app/console '.$daemon.' start';
                            }
                        }
                    });
                }
            }
        }

        if (!empty($startDaemons)) {
            $allDaemonsProcess = new Process($startDaemons);
            $allDaemonsProcess->run(function ($err) {
                if (Process::ERR === $err) {
                    die('Cannot start daemons');
                } else {
                    die('Some daemons were broken out, but successfully restarted');
                }
            });
        } else {
            die('All daemons successfully checked and sated up');
        }
    }
}
