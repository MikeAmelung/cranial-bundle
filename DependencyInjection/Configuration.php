<?php

namespace MikeAmelung\CranialBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('mike_amelung_cranial');

        $treeBuilder
            ->getRootNode()
            ->children()
            ->scalarNode('config_directory')
            ->end()
            ->scalarNode('image_directory')
            ->end()
            ->scalarNode('image_path_prefix')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
