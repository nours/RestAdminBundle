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
 * Class ViewHandlerPass
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ViewHandlerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('rest_admin.view_handler');

        $ids = $container->findTaggedServiceIds('rest_admin.view_handler');
        foreach ($ids as $id => $tags) {
            $definition->addMethodCall('addHandler', array(new Reference($id)));
        }
    }
}