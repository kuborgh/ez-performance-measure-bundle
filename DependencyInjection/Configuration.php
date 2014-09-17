<?php

namespace Kuborgh\Bundle\MeasureBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kuborgh_measure');

        $rootNode->children()
            ->arrayNode('content_type_list_measurer')
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->children()
                    ->scalarNode('service')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
        $rootNode->children()
            ->arrayNode('content_type_single_measurer')
                ->useAttributeAsKey('name')
                ->prototype('array')
                ->children()
                    ->scalarNode('service')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
