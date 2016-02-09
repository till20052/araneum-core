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
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configDaemonsArray = $this->getContainer()->getParameter('mik_software_daemon.daemons');
        $daemonsArray = [];
        foreach ($configDaemonsArray as $name=>$params) {
            array_push($daemonsArray, preg_replace('/_/', ':', $name));
        }
        $startDaemons = '';
        foreach ($daemonsArray as $i=>$daemon) {
            if (!in_array($daemon, self::BROKEN_DAEMONS)) {
                $process = new Process('app/console '.$daemon.' status');
                if (!$process->isRunning()) {
                    $process->run(function($err, $data) use (&$startDaemons, &$daemon, &$i){
                        if (Process::ERR === $err) {
                            die('Cannot get daemon status: '.$daemon);
                        } elseif ($data != 'true'){
                            if ($i != 0){
                                $startDaemons .= ' && ';
                            }
                            $startDaemons .= 'app/console '.$daemon.' start';
                        }
                    });
                }
            }
        }
        if (!empty($startDaemons)) {
            $allDaemonsProcess = new Process($startDaemons);
            $allDaemonsProcess->run(function($err, $data){
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
