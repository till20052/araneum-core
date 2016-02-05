<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Base\Exception\InvalidFormException;
use MikSoftware\DaemonBundle\Commnad\DaemonizedCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DaemonCheckerCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class DaemonCheckerCommand extends DaemonizedCommand
{
    protected static $COMMANDS = [
        'connection'    => 'AraneumMainBundle:Connection',
        'cluster'       => 'AraneumMainBundle:Cluster',
        'application'   => 'AraneumMainBundle:Application',
        'runner'        => 'AraneumMainBundle:Runner',
    ];

    /**
     * Configures command.
     */
    protected function configureDaemonCommand()
    {
        $this
            ->setName('araneum:daemon:checker')
            ->setDescription('Used for demonize. Checker check.')
            ->setHelp('Usage <info>php app/console <name> start|stop|restart</info>');
    }

    /**
     * Execute command
     *
     * @inheritdoc
     */
    protected function daemonLogic()
    {
        $this->getContainer()->get('logger')->info("Daemon araneum:daemon:checker is running!");
        $daemonInterval = $this->getContainer()->getParameter('araneum_daemon_checker_iterate');
        $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
        $em = $this->getContainer()
            ->get('doctrine')
            ->getManager();

        if (empty($daemonInterval)) {
            throw new InvalidFormException('Interval daemon should not be less than zero.');
        }

        foreach (self::$COMMANDS as $command => $repository) {
            if ($items = $em->getRepository($repository)->findAll()) {
                foreach ($items as $item) {
                     $commandRunner->runSymfonyCommandInNewProcess("checker:check {$command} {$item->getId()}", $this);
                }
            }
        }

        $this->getDaemon()
            ->iterate($this->convertInterval($daemonInterval));
    }

    /**
     * @param $intr
     * @return int
     */
    protected function convertInterval($intr)
    {
        $seconds = 0;
        $interval = new \DateInterval('PT'.strtoupper($intr));

        switch ($interval) {
            case ($interval->h !== 0):
                $seconds = $interval->h*3600;
                break;
            case ($interval->i !== 0):
                $seconds = $interval->i*60;
                break;
            case ($interval->s !== 0):
                $seconds = $interval->s;
                break;
            default:
                $intr = (int) $intr;
                if ($intr) {
                    $seconds = $intr;
                }
                break;
        }

        return $seconds;
    }
}
