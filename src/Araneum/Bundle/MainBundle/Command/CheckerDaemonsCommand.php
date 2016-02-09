<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
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
        foreach ($daemonsArray as $daemon) {
            if (!in_array($daemon, self::BROKEN_DAEMONS)) {
                $process = new Process('app/console '.$daemon.' status');
                $process->run(function ($err, $data) use (&$daemon) {
                    if (Process::ERR === $err) {
                        $this->output->writeln('Cannot get daemon status: '.$daemon);
                    } elseif ($data == 'Daemon doesn\'t work') {
                        (new Process('app/console '.$daemon.' start'))->run();
                        $this->output->writeln($daemon.' was broken, but starts to work now');
                    }
                });
            }
        }
            $this->output->writeln('All daemons successfully checked and sated up');
    }
}
