<?php

namespace Araneum\Base\Service;

use Symfony\Component\Process\Process;

/**
 * Class CommandRunnerService
 *
 * @package Araneum\Base\Service
 */
class CommandRunnerService
{
    private $rootDir;

    /**
     * construct
     *
     * @param string $kernelRootDir
     */
    public function __construct($kernelRootDir)
    {
        $this->rootDir = $kernelRootDir;
    }

    /**
     * Run Symfony command in new process.
     *
     * @param string $input
     */
    public function runSymfonyCommandInNewProcess($input)
    {
        $input = $this->rootDir.'/console '.$input;
        $deploymentCommand = new Process($input, null, null, null, null);
        $deploymentCommand->mustRun();
    }

    /**
     * Run Symfony deployment command as separate process.
     *
     * @param  string $command
     * @param  string $path
     * @return string
     */
    public function runDeployCommandsInSeparateProcess($command, $path = '')
    {
        $deploymentCommand = new Process($command, $this->rootDir.$path, null, null, null);
        $deploymentCommand->start();
        $message = '';
        $deploymentCommand->wait(
            function ($type, $buffer) use (&$message) {
                if (Process::ERR === $type) {
                    $message = 'ERR > '.$buffer;
                } else {
                    $message = 'OUT > '.$buffer;
                }
            }
        );

        return $message;
    }
}
