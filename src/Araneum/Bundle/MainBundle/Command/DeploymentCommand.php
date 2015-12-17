<?php

namespace Araneum\Bundle\MainBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DeploymentCommand
 *
 * @package Araneum\Bundle\MainBundle\Command
 */
class DeploymentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('deployment:run')
            ->setDescription('Deploy active project. Run all deployment command in one time.');
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return null;
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commands = [
            'cache:accelerator:clear' => [],
            'cache:clear' => [
                '--no-debug' => true,
            ],
            'doctrine:schema:update' => [
                '--force' => true,
            ],
            'doctrine:fixtures:load' => [
                '--append' => true,
            ],
        ];

        foreach ($commands as $commandName => $options) {
            $output->writeln('<comment>Run command: '.$commandName.'</comment>');

            $commandOptions = [];
            $command = $this->getApplication()->find($commandName);
            foreach ($input->getOptions() as $inputOptionName => $inputOptionValue) {
                if ($command->getDefinition()->hasOption($inputOptionName)) {
                    $commandOptions['--'.$inputOptionName] = $inputOptionValue;
                }
            }

            $command->run(
                new ArrayInput(array_merge($commandOptions, $options)),
                $output
            );
        }

        $output->writeln('Deployment done!');
    }
}
