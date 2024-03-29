<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\DependencyInjection\Compiler;

use DomainException;
use Nours\RestAdminBundle\EventListener\ParamFetcherListener;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Injects param fetchers with rest_admin.param_fetcher tag into the event listener
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ParamFetcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $interface  = 'Nours\RestAdminBundle\ParamFetcher\ParamFetcherInterface';
        $definition = $container->getDefinition(ParamFetcherListener::class);

        $ids = $container->findTaggedServiceIds('rest_admin.param_fetcher');
        $fetchers = $references = [];
        foreach ($ids as $id => $tags) {
            if (!isset($tags[0]['alias'])) {
                throw new DomainException(sprintf(
                    "Service %s rest_admin.param_fetcher tag must have alias parameter",
                    $id
                ));
            }

            $alias = $tags[0]['alias'];

            if (isset($fetchers[$alias])) {
                throw new DomainException(sprintf(
                    "Param fetcher %s is declared twice (registered : %s, found : %s)",
                    $alias, $fetchers[$alias], $id
                ));
            }

            // Check service implements the interface
            $refl = new ReflectionClass($container->getDefinition($id)->getClass());
            if (!$refl->implementsInterface($interface)) {
                throw new DomainException(sprintf(
                    "Param fetcher service %s (%s) must implement %s",
                    $id, $refl->getName(), $interface
                ));
            }

            $fetchers[$alias] = $id;
            $references[$id] = new Reference($id);
        }

        $definition->replaceArgument(1, $fetchers);

        if (class_exists(ServiceLocatorTagPass::class)) {
            // Replace container with service locator
            $definition->replaceArgument(0, ServiceLocatorTagPass::register($container, $references));
        }
    }
}