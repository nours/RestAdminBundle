<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle;

use Nours\RestAdminBundle\DependencyInjection\Compiler\ViewHandlerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Nours\RestAdminBundle\DependencyInjection\Compiler\ActionManagerPass;
use Nours\RestAdminBundle\DependencyInjection\Compiler\LoaderResolverPass;

/**
 * Class NoursRestAdminBundle
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class NoursRestAdminBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ActionManagerPass());
        $container->addCompilerPass(new LoaderResolverPass());
        $container->addCompilerPass(new ViewHandlerPass());
    }
}
