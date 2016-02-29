<?php

namespace Araneum\Bundle\MainBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MikSoftwareDaemonCompilePass implements CompilerPassInterface
{
    /**
     * Process container
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $daemonService = $container->getDefinition('mik_software.daemon_service');
        if ($daemonService) {
            $daemonService->setClass('Araneum\Base\MikSoftware\DaemonBundle\Services\DaemonService');
        }
    }
}
