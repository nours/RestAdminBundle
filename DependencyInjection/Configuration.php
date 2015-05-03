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
                ->booleanNode('orm')->defaultTrue()->end()
                ->arrayNode('listeners')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('orm')->defaultFalse()->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->defaultValue('NoursRestAdminBundle:Core:index.html.twig')->end()
                        ->scalarNode('get')->defaultValue('NoursRestAdminBundle:Core:get.html.twig')->end()
                        ->scalarNode('create')->defaultValue('NoursRestAdminBundle:Core:create.html.twig')->end()
                        ->scalarNode('edit')->defaultValue('NoursRestAdminBundle:Core:edit.html.twig')->end()
                        ->scalarNode('delete')->defaultValue('NoursRestAdminBundle:Core:delete.html.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('controllers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->defaultValue('rest_admin.core_controller:indexAction')->end()
                        ->scalarNode('get')->defaultValue('rest_admin.core_controller:getAction')->end()
                        ->scalarNode('create')->defaultValue('rest_admin.core_controller:createAction')->end()
                        ->scalarNode('edit')->defaultValue('rest_admin.core_controller:editAction')->end()
                        ->scalarNode('delete')->defaultValue('rest_admin.core_controller:deleteAction')->end()
                    ->end()
                ->end()
//                ->arrayNode('default_templates')
//                    ->addDefaultsIfNotSet()
//                    ->children()
//                        ->scalarNode('layout')->defaultValue('NoursRestAdminBundle::layout.html.twig')->end()
//                        ->scalarNode('layout_ajax')->defaultValue('NoursRestAdminBundle::layout_ajax.html.twig')->end()
//                        ->scalarNode('cget')->defaultValue('NoursRestAdminBundle:Read:cget.html.twig')->end()
//                        ->scalarNode('get')->defaultValue('NoursRestAdminBundle:Read:get.html.twig')->end()
//                        ->scalarNode('new')->defaultValue('NoursRestAdminBundle:Resource:form.html.twig')->end()
//                        ->scalarNode('edit')->defaultValue('NoursRestAdminBundle:Resource:form.html.twig')->end()
//                        ->scalarNode('remove')->defaultValue('NoursRestAdminBundle:Resource:remove.html.twig')->end()
//                    ->end()
//                ->end()
//                ->arrayNode('default_attributes')
//                    ->prototype('variable')
//                    ->end()
//                ->end()
//                ->scalarNode('translation_prefix')->defaultValue('admin')->end()
//                ->scalarNode('index_route')->isRequired()->end()
//                ->scalarNode('route_prefix')->defaultValue('admin_')->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
