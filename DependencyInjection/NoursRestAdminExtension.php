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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class NoursRestAdminExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('twig.yml');

        if ($config['listeners']['orm']) {
            $loader->load('orm.yml');
        }

        if ($config['listeners']['view']) {
            $loader->load('view.yml');
        }

        $container->setParameter('rest_admin.resource', $config['resource']);

        // Domain classes
        $this->configureDomainClasses($config, $container);

        // Default templates
        foreach ($config['templates'] as $action => $template) {
            $container->setParameter('rest_admin.templates.' . $action, $template);
        }

        // Default controllers
        foreach ($config['controllers'] as $action => $controller) {
            $container->setParameter('rest_admin.controllers.' . $action, $controller);
        }

        // Default forms
        foreach ($config['forms'] as $action => $form) {
            $container->setParameter('rest_admin.forms.' . $action, $form);
        }

        // Services
        foreach ($config['services'] as $name => $service) {
            $container->setAlias('rest_admin.' . $name, $service);
        }
    }


    private function configureDomainClasses(array $config, ContainerBuilder $container)
    {
        $resourceClass = $config['resource_class'];
        $subClass = 'Nours\\RestAdminBundle\\Domain\\Resource';
        $reflection = new \ReflectionClass($resourceClass);
        if ($resourceClass !== $subClass && !$reflection->isSubclassOf($subClass)) {
            throw new \DomainException("Resource class $resourceClass must extend base class $subClass");
        }

        $container->setParameter('rest_admin.resource_class', $resourceClass);
    }
}
