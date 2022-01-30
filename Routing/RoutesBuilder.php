<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Routing;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Event\RestAdminEvents;
use Nours\RestAdminBundle\Event\RouteEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;


/**
 * Class RoutesBuilder
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RoutesBuilder
{
    private $collection;
    private $eventDispatcher;

    /**
     * @param RouteCollection $collection
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(RouteCollection $collection, EventDispatcherInterface $eventDispatcher)
    {
        $this->collection = $collection;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Action $action
     * @param string $routeSuffix
     * @param string|string[] $method
     * @param $path
     * @param array $defaults
     * @param array $requirements
     * @param array $options
     */
    public function addRoute(
        Action $action, string $routeSuffix, $method, $path,
        array $defaults = [], array $requirements = [],
        array $options = []
    ) {
        $resource = $action->getResource();

        $defaults = array_merge(array(
            '_resource'   => $resource->getFullName(),
            '_action'     => $action->getName(),
            '_controller' => $action->getController(),
            '_format'     => null
        ), $defaults);

//        $path = $action->get

        // Dispatch the route event
        $event = new RouteEvent(
            $resource, $action,
            $path . '.{_format}',
            $defaults,
            $requirements,  // Requirements
            $options,       // Options
            $method
        );

        $this->eventDispatcher->dispatch($event, RestAdminEvents::ROUTE);

        $route = new Route(
            $event->path, $event->defaults, $event->requirements, $event->options, $event->host, $event->schemes, $event->method
        );

        $this->collection->add($resource->getRouteName($routeSuffix), $route);
    }
}