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
        $deploymentCommand = new Process($input);
        $deploymentCommand->mustRun();
    }

    /**
     * Run Symfony deployment command as separate process.
     *
     * @param  string $comand
     * @param  string $path
     * @return string
     */
    public function runDeployCommandsInSeparateProcess($comand, $path = '')
    {
        $deploymentCommand = new Process($comand, $this->rootDir.$path, null, null, null);
        $deploymentCommand->start();
        $message = '';
        $deploymentCommand->wait(
            function ($type, $buffer) use (&$message) {
                if (Process::ERR === $type) {
                    var_dump($buffer);
                    $message = 'ERR > '.$buffer;
                } else {
                    $message = 'OUT > '.$buffer;
                }
            }
        );

        return $message;
    }
}
