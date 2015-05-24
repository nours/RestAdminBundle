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
use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Default action builder.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DefaultActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function createAction(Resource $resource, array $options = array())
    {
        // The action name is passed from options
        $options = $this->resolveOptions($options);
        $name = $options['name'];
        unset($options['name']);

        return new Action($name, $resource, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Resource $resource, Action $action)
    {
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
        $resolver->setDefaults(array(
            'template' => '',
            'type'     => 'default',
            'routes'   => array()
        ));
        $resolver->setRequired(array('name'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'default';
    }
}