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

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ControllerBuildersPass
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class BuilderManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('rest_admin.action_manager');

        $ids = $container->findTaggedServiceIds('rest_admin.action_builder');
        foreach ($ids as $id => $tags) {
            $definition->addMethodCall('addActionBuilder', array(new Reference($id)));
        }

        $ids = $container->findTaggedServiceIds('rest_admin.event_builder');
        foreach ($ids as $id => $tags) {
            $definition->addMethodCall('addEventBuilder', array(new Reference($id)));
        }
    }
}