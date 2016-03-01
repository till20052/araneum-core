<?php

namespace Araneum\Bundle\MainBundle;

use Araneum\Bundle\MainBundle\DependencyInjection\MikSoftwareDaemonCompilePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class AraneumMainBundle
 *
 * @package Araneum\Bundle\MainBundle
 */
class AraneumMainBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new MikSoftwareDaemonCompilePass());
    }
}
