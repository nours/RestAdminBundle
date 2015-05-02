<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Routing;

use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\Api\KernelProvider;
use Nours\RestAdminBundle\ActionManager;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;

/**
 * RoutingLoader for admin controllers.
 *
 * Delegates the route collection construction to controller builders.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RoutingLoader extends Loader
{
    /**
     * @var \Nours\RestAdminBundle\Domain\ResourceCollection
     */
    private $collection;

    /**
     * @var ActionManager
     */
    private $builders;

    /**
     * @param AdminManager $manager
     * @param ActionManager $factory
     */
    public function __construct(AdminManager $manager, ActionManager $factory)
    {
        $this->collection = $manager->getResourceCollection();
        $this->builders = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $routes = new RouteCollection();
        $routesBuilder = new RoutesBuilder($routes);

        // Iterate on resources
        foreach ($this->collection as $resource) {
            /** @var \Nours\RestAdminBundle\Domain\Resource $resource */

            foreach ($resource->getActions() as $action) {
                $builder = $this->builders->getActionBuilder($action->getName());
                $builder->buildRoutes($routesBuilder, $resource, $action);
            }
        }

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type == 'admin';
    }
}