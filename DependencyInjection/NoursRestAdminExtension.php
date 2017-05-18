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
        $loader->load('table.yml');

        /*
         * Table extension supports automatic filter from parent resources
         *
         * Disabled by default, as it might break existing code.
         */
        if ($config['table_extension_disable_child_filter']) {
            trigger_error("Table extension child resources filter being disabled by default is deprecated. " .
                          "Please activate it and check custom code.", E_USER_DEPRECATED);
        }
        $container->setParameter('rest_admin.table.disable_child_filter', $config['table_extension_disable_child_filter']);

        // ORM integration
        if ($config['listeners']['orm']) {
            $loader->load('orm.yml');
        }

        // View handlers
        if ($config['listeners']['view']) {
            $loader->load('view.yml');
        }

        // Security
        if ($config['listeners']['security']) {
            $loader->load('security.yml');
        }

        // Knp Menu voter
        if ($config['knp_menu_voter']) {
            $loader->load('menu.yml');
        }

        $container->setParameter('rest_admin.listeners.templating.formats', $config['templating_formats']);
        $container->setParameter('rest_admin.resource', $config['resource']);
        $container->setParameter('rest_admin.default_param_fetcher', $config['default_param_fetcher']);

        // Domain classes
        $this->configureDomainClasses($config, $container);

        // Action params
        $this->configureActionParams($config, $container);

        // Service aliases (see Configuration for default values)
        foreach ($config['services'] as $name => $service) {
            if ($service) {
                $container->setAlias('rest_admin.' . $name, $service);
            }
        }

        // Action template parameter
        $container->setParameter('rest_admin.template.action', $config['templates']['action']);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function configureActionParams(array $config, ContainerBuilder $container)
    {
        // Global defaults
        $defaults = $config['extras']['defaults'];

        $actionTypes = array('index', 'get', 'create', 'edit', 'copy', 'delete', 'bulk_delete', 'form', 'custom');
        foreach ($actionTypes as $actionType) {
            $params = $defaults;

            // Controller + template
            if (array_key_exists($actionType, $config['controllers'])) {
                $params['controller'] = $config['controllers'][$actionType];
            }
            if (array_key_exists($actionType, $config['templates'])) {
                $params['template'] = $config['templates'][$actionType];
            }

            // Form (optional)
            if (array_key_exists($actionType, $config['forms'])) {
                $params['form'] = $config['forms'][$actionType];
            }

            // Extras
            if (isset($config['extras'][$actionType])) {
                $extras = $config['extras'][$actionType];
                if (!is_array($extras)) {
                    throw new \DomainException("Extra params for action type $actionType must be an array");
                }

                foreach ($extras as $extra => $value) {
                    $params[$extra] = $value;
                }
            }

            $container->setParameter('rest_admin.actions.' . $actionType, $params);
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    private function configureDomainClasses(array $config, ContainerBuilder $container)
    {
        $resourceClass = $config['resource_class'];
        $subClass = 'Nours\\RestAdminBundle\\Domain\\DomainResource';
        $reflection = new \ReflectionClass($resourceClass);
        if ($resourceClass !== $subClass && !$reflection->isSubclassOf($subClass)) {
            throw new \DomainException("Resource class $resourceClass must extend base class $subClass");
        }

        $container->setParameter('rest_admin.resource_class', $resourceClass);
    }
}
