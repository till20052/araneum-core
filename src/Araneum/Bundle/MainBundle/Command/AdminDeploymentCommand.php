<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class AdminDeploymentCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class AdminDeploymentCommand extends ContainerAwareCommand
{
    /**
     * Configure command
     */
    protected function configure()
    {
        $this->setName('front:admin:build')
            ->setDescription('Build front application for admin panel.');
    }

    /**
     * {@inheritdoc}
     * Execute command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pathToRun = '/../web/admin/source/require';
        $output->writeln('<info>Run admin application building...</info>');

        $commands = [
            'npm install',
            'bower install --allow-root --config.interactive=false',
            'gulp build',
        ];

        foreach ($commands as $command) {
            $output->writeln('<comment>Run command: '.$command.'</comment>');

            $commandRunner = $this->getContainer()->get('araneum.command_runner.service');
            $resultMessage = $commandRunner->runDeployCommandsInSeparateProcess($command, $pathToRun);
            $output->write($resultMessage);

            if ($command != 'npm install' && strpos($resultMessage, 'ERR') === 0) {
                $output->writeln('Process aborted');
                break;
            }
        }

        $output->writeln('Building done!');
    }
}
