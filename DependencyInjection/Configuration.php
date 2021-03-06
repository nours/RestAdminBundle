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

use Nours\RestAdminBundle\Form\Type\BulkDeleteType;
use Nours\RestAdminBundle\Form\Type\DeleteType;
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
                ->scalarNode('default_param_fetcher')->defaultValue('orm')->end()
                ->scalarNode('resource_class')
                    ->defaultValue('Nours\RestAdminBundle\Domain\DomainResource')
                    ->info('Main resource definition class, redefine to use your own implementation')
                ->end()
                ->scalarNode('table_extension_disable_child_filter')->defaultTrue()->end()
                ->arrayNode('listeners')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('view')->defaultTrue()
                            ->info('View handling, activated by default')
                        ->end()
                        ->booleanNode('orm')->defaultFalse()
                            ->info('ORM integration')
                        ->end()
                        ->booleanNode('security')->defaultFalse()
                            ->info('Enables security for resource access')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('knp_menu_voter')->defaultTrue()->end()
                ->arrayNode('templating_formats')
                    ->prototype('scalar')->end()
                    ->defaultValue(array('html'))
                ->end()
                ->arrayNode('extras')
                    ->children()
                        ->variableNode('defaults')->defaultValue(array())->end()
                        ->variableNode('index')->defaultValue(array())->end()
                        ->variableNode('get')->defaultValue(array())->end()
                        ->variableNode('create')->defaultValue(array())->end()
                        ->variableNode('edit')->defaultValue(array())->end()
                        ->variableNode('copy')->defaultValue(array())->end()
                        ->variableNode('delete')->defaultValue(array())->end()
                        ->variableNode('bulk_delete')->defaultValue(array())->end()
                        ->variableNode('form')->defaultValue(array())->end()
                        ->variableNode('custom')->defaultValue(array())->end()
                    ->end()
                ->end()
                ->arrayNode('templates')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->isRequired()->end()
                        ->scalarNode('get')->isRequired()->end()
                        ->scalarNode('create')->isRequired()->end()
                        ->scalarNode('edit')->isRequired()->end()
                        ->scalarNode('copy')->defaultNull()->end()
                        ->scalarNode('delete')->isRequired()->end()
                        ->scalarNode('bulk_delete')->isRequired()->end()
                        ->scalarNode('form')->defaultNull()->end()
                        ->scalarNode('action')->defaultValue('NoursRestAdminBundle::action.html.twig')->end()
                    ->end()
                ->end()
                ->arrayNode('controllers')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('index')->defaultValue('NoursRestAdminBundle:Default:index')->end()
                        ->scalarNode('get')->defaultValue('NoursRestAdminBundle:Default:get')->end()
                        ->scalarNode('create')->defaultValue('NoursRestAdminBundle:Default:form')->end()
                        ->scalarNode('edit')->defaultValue('NoursRestAdminBundle:Default:form')->end()
                        ->scalarNode('copy')->defaultValue('NoursRestAdminBundle:Default:form')->end()
                        ->scalarNode('delete')->defaultValue('NoursRestAdminBundle:Default:form')->end()
                        ->scalarNode('bulk_delete')->defaultValue('NoursRestAdminBundle:Default:form')->end()
                        ->scalarNode('form')->defaultValue('NoursRestAdminBundle:Default:form')->end()
                    ->end()
                ->end()
                ->arrayNode('forms')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('delete')->defaultValue(DeleteType::class)->end()
                        ->scalarNode('bulk_delete')->defaultValue(BulkDeleteType::class)->end()
                    ->end()
                ->end()
                ->arrayNode('services')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('serializer')->defaultValue('serializer')->end()
                        ->scalarNode('serialization_context')->defaultNull()->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
