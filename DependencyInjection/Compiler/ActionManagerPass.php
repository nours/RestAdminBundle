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

use Nours\RestAdminBundle\ActionManager;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ActionManagerPass
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionManagerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(ActionManager::class);

        $ids = $container->findTaggedServiceIds('rest_admin.action_builder');
        foreach ($ids as $id => $tags) {
            $definition->addMethodCall('addActionBuilder', array(new Reference($id)));
        }
    }
}