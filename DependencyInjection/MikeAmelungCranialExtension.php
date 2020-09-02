<?php

namespace MikeAmelung\CranialBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class MikeAmelungCranialExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'mike_amelung_cranial.config_directory',
            $config['config_directory']
        );
        $container->setParameter(
            'mike_amelung_cranial.storage',
            $config['storage']
        );
        $container->setParameter(
            'mike_amelung_cranial.content_directory',
            $config['content_directory']
        );
        $container->setParameter(
            'mike_amelung_cranial.image_directory',
            $config['image_directory']
        );
        $container->setParameter(
            'mike_amelung_cranial.image_path_prefix',
            $config['image_path_prefix']
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );
        $loader->load('services.yaml');
    }
}
