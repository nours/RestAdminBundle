<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Action\Core;

use Nours\RestAdminBundle\Action\AbstractBuilder;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom action builder.
 *
 * The routing of these actions is user custom,
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CustomActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Action $action)
    {
        $resource = $action->getResource();
        foreach ($action->getConfig('routes') as $route) {
            $builder->addRoute(
                $resource, $action,
                $route['name'],
                $route['methods'],
                $resource->getUriPath($route['path']),
                $route['defaults'],
                $route['requirements'],
                $route['options']
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        // Overrides default options :
        $resolver->setDefaults(array(
            'type'     => 'custom',    // this builder's type is default
            'name'     => null,
            'template' => '',
            'routes'   => array(),
            'read_only' => function(Options $options) {
                // Read Only can be determined automatically from routes
                $readOnly = true;
                foreach ($options['routes'] as $route) {
                    foreach ($route['methods'] as $method) {
                        if ($method != 'GET') {
                            $readOnly = false;
                        }
                    }
                }

                return $readOnly;
            }
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'custom';
    }
}