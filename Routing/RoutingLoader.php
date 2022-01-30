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

use Nours\RestAdminBundle\Action\ActionBuilderInterface;
use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
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
        parent::__construct();

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
            /** @var DomainResource $resource */

            // The get action routes must be appended after others
            // (otherwise it will conflicts with global resource routes)
            $getAction = null;

            foreach ($resource->getActions() as $action) {
                if ($action->getName() == 'get') {
                    $getAction = $action;
                } else {
                    $this->getActionBuilder($action)->buildRoutes($routesBuilder, $action);
                }
            }

            if ($getAction) {
                $this->getActionBuilder($getAction)->buildRoutes($routesBuilder, $getAction);
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
    public function supports($resource, string $type = null): bool
    {
        return $type == 'admin';
    }

    /**
     * @param Action $action
     * @return ActionBuilderInterface
     */
    private function getActionBuilder(Action $action): ActionBuilderInterface
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