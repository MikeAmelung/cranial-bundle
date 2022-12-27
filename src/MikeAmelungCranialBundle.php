<?php

namespace MikeAmelung\CranialBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use MikeAmelung\CranialBundle\DependencyInjection\Configuration;

class MikeAmelungCranialBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
            ->children()
            ->scalarNode('config_directory')
            ->end()
            ->end();
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $bundles = $builder->getParameter('kernel.bundles');

        if (isset($bundles['DoctrineBundle'])) {
            $loader = new YamlFileLoader(
                $builder,
                new FileLocator(__DIR__ . '/../config')
            );
            $loader->load('doctrine.yaml');
        }
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter(
            'mike_amelung_cranial.config_directory',
            $config['config_directory']
        );

        $loader = new YamlFileLoader(
            $builder,
            new FileLocator(__DIR__ . '/../config')
        );
        $loader->load('services.yaml');
    }
}
