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
 * Class LoaderResolverPass
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class LoaderResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('rest_admin.loader_resolver');

        $ids = $container->findTaggedServiceIds('rest_admin.loader');
        foreach ($ids as $id => $tags) {
            $definition->addMethodCall('addLoader', array(new Reference($id)));
        }
    }
}