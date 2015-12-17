<?php

namespace Araneum\Bundle\MainBundle\Command;

use Araneum\Bundle\MainBundle\Entity\Application;
use Araneum\Bundle\MainBundle\Entity\Cluster;
use Araneum\Bundle\MainBundle\Entity\Connection;
use Araneum\Bundle\MainBundle\Service\ApplicationCheckerService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CheckerCheckCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class CheckerCheckCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var FormatterHelper
     */
    private $formatter;

    /**
     * @var ApplicationCheckerService
     */
    private $checker;

    /**
     * Configure Command
     */
    protected function configure()
    {
        $this
            ->setName('checker:check')
            ->setDescription('Check cluster or application state status.')
            ->addArgument(
                'target',
                InputArgument::REQUIRED,
                'Need to set target. The target may be: connection, application'
            )
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'Target ID. Need this option to find target entity'
            );
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
        $this->output = $output;

        $targets = [
            'connection',
            'cluster',
            'application',
        ];

        $target = strtolower($input->getArgument('target'));
        $targetId = intval($input->getArgument('id'));

        if (!in_array($target, $targets)) {
            $this->showError(
                sprintf(
                    'This target "%s" is not isset in list of arguments: %s',
                    $target,
                    implode(', ', $targets)
                )
            );

            return;
        }

        if (!($targetId > 0)) {
            $this->showError(
                sprintf(
                    'ID has not valid value or %s Entity does not exists by this ID',
                    ucfirst($target)
                )
            );

            return;
        }

        /** @var ApplicationCheckerService $checker */
        $this->checker = $this->getContainer()->get('araneum.main.application.checker');

        if ($target == 'connection') {
            $this->checkConnection($targetId);
        } elseif ($target == 'cluster') {
            $this->checkCluster($targetId);
        } elseif ($target == 'application') {
            $this->checkApplication($targetId);
        }
    }

    /**
     * Get Formatted Notification Block
     *
     * @param            $messages
     * @param            $style
     * @param bool|false $large
     * @return string
     */
    private function getFormatBlock($messages, $style, $large = false)
    {
        if (!$this->formatter instanceof FormatterHelper) {
            $this->formatter = $this->getHelper('formatter');
        }

        return $this->formatter->formatBlock($messages, $style, $large);
    }

    /**
     * Show Error Message
     *
     * @param $message
     */
    private function showError($message)
    {
        $this->output->writeln("\n".$this->getFormatBlock($message, 'error', true));
    }

    /**
     * Show Info Message
     *
     * @param $message
     */
    private function showInfo($message)
    {
        $this->output->writeln($this->getFormatBlock($message, 'info'));
    }

    /**
     * Check Connection status state
     *
     * @param $id
     */
    private function checkConnection($id)
    {
        $status = $this->checker->checkConnection($id);

        $this->showInfo(
            sprintf(
                "Connection\n ID: %d\n Status: %s",
                $id,
                Connection::getStatusDescription($status)
            )
        );
    }

    /**
     * Check Cluster status state
     *
     * @param $id
     */
    private function checkCluster($id)
    {
        $status = $this->checker->checkCluster($id);

        $this->showInfo(
            sprintf(
                "Cluster\n ID: %d\n Status: %s",
                $id,
                Cluster::getStatusDescription($status)
            )
        );
    }

    /**
     * Check Application status state
     *
     * @param $id
     */
    private function checkApplication($id)
    {
        $status = $this->checker->checkApplication($id);

        $this->showInfo(
            sprintf(
                "Application\n ID: %d\n Status: %s",
                $id,
                Application::getStatusDescription($status)
            )
        );
    }
}
