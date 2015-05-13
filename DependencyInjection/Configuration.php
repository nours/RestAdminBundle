<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('nours_rest_admin');
        
        $rootNode
            ->children()
                ->scalarNode('resource')->isRequired()->end()
                ->scalarNode('resource_class')->defaultValue('Nours\RestAdminBundle\Domain\Resource')->end()
                ->arrayNode('listeners')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('orm')->defaultTrue()->end()
                        ->booleanNode('view')->defaultTrue()->end()
                    ->end()
                ->end()
                ->arrayNode('extras')
                    ->children()
                        ->variableNode('index')->defaultValue(array())->end()
                        ->variableNode('get')->defaultValue(array())->end()
                        ->variableNode('create')->defaultValue(array())->end()
                        ->variableNode('edit')->defaultValue(array())->end()
                        ->variableNode('delete')->defaultValue(array())->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->isRequired()->end()
                        ->scalarNode('get')->isRequired()->end()
                        ->scalarNode('create')->isRequired()->end()
                        ->scalarNode('edit')->isRequired()->end()
                        ->scalarNode('delete')->isRequired()->end()
                    ->end()
                ->end()
                ->arrayNode('controllers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->defaultValue('NoursRestAdminBundle:Default:index')->end()
                        ->scalarNode('get')->defaultValue('NoursRestAdminBundle:Default:get')->end()
                        ->scalarNode('create')->defaultValue('NoursRestAdminBundle:Default:create')->end()
                        ->scalarNode('edit')->defaultValue('NoursRestAdminBundle:Default:edit')->end()
                        ->scalarNode('delete')->defaultValue('NoursRestAdminBundle:Default:delete')->end()
                    ->end()
                ->end()
                ->arrayNode('forms')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('create')->defaultNull()->end()
                        ->scalarNode('edit')->defaultNull()->end()
                        ->scalarNode('delete')->defaultValue('rest_admin_delete')->end()
                    ->end()
                ->end()
                ->arrayNode('services')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('serializer')->defaultValue('serializer')->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
