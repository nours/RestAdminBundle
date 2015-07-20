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
use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
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
     * @var AdminManager
     */
    private $manager;

    /**
     * @var ActionManager
     */
    private $builders;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @param AdminManager $manager
     * @param ActionManager $factory
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(AdminManager $manager, ActionManager $factory, EventDispatcherInterface $eventDispatcher)
    {
        $this->manager  = $manager;
        $this->builders = $factory;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $resources = $this->manager->getResourceCollection();

        $routes = new RouteCollection();
        $routesBuilder = new RoutesBuilder($routes, $this->eventDispatcher);

        // Iterate on resources
        foreach ($resources as $resource) {
            /** @var \Nours\RestAdminBundle\Domain\Resource $resource */

            foreach ($resource->getActions() as $action) {
                $builder = $this->getActionBuilder($action);
                $builder->buildRoutes($routesBuilder, $action);
            }
        }

        // Append config resources to routing collection
        foreach ($resources->getConfigResources() as $res) {
            $routes->addResource($res);
        }
        $routes->addResource(new FileResource(__FILE__));

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type == 'admin';
    }

    /**
     * @param Action $action
     * @return \Nours\RestAdminBundle\Action\ActionBuilderInterface
     */
    private function getActionBuilder(Action $action)
    {
        try {
            return $this->builders->getActionBuilder($action->getType());
        } catch (\InvalidArgumentException $e) {
            throw new \DomainException(sprintf(
                'Cannot find builder for action %s of resource %s',
                $action->getName(), $action->getResource()->getFullName()
            ), 0, $e);
        }
    }
}