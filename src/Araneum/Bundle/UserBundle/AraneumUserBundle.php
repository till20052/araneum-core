<?php

namespace Araneum\Bundle\UserBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AraneumUserBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $mappings = [
            realpath(__DIR__ . '/Resources/config/doctrine/model') => 'FOS\UserBundle\Entity',
        ];

        $container->addCompilerPass(
            DoctrineOrmMappingsPass::createXmlMappingDriver(
                $mappings,
                ['fos_user.model_manager_name'],
                false
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
