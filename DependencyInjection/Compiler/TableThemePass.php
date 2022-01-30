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

/**
 * Class TableThemePass
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class TableThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if ($templates = $container->getParameter('nours_table.themes')) {
            array_unshift($templates, 'NoursRestAdminBundle::table.html.twig');
            $container->setParameter('nours_table.themes', $templates);
        }
    }
}