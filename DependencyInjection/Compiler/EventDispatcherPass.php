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
 * Class EventDispatcherPass
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class EventDispatcherPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('rest_admin.event_dispatcher');

        $ids = $container->findTaggedServiceIds('rest_admin.event_listener');
        foreach ($ids as $id => $events) {


//            $definition->addMethodCall('addEventListener', array(new Reference($id)));
        }

        $ids = $container->findTaggedServiceIds('rest_admin.event_subscriber');
        foreach ($ids as $id => $tags) {
            $definition->addMethodCall('addEventSubscriber', array(new Reference($id)));
        }
    }
}