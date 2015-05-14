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
use Nours\RestAdminBundle\Domain\Resource;
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
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @param $routeName
     * @param $method
     * @param $path
     */
    public function addRoute(Resource $resource, Action $action, $routeName, $method, $path)
    {
        $defaults = array(
            '_resource'   => $resource->getFullName(),
            '_action'     => $action->getName(),
            '_controller' => $action->getController(),
            '_format'     => null
        );

        // Dispatch the route event
        $event = new RouteEvent(
            $resource, $action,
            $path . '.{_format}',
            $defaults,
            array(),    // Requirements
            array(),    // Options
            $method
        );

        $this->eventDispatcher->dispatch(RestAdminEvents::ROUTE, $event);

        $route = new Route(
            $event->path, $event->defaults, $event->requirements, $event->options, $event->host, $event->schemes, $event->method
        );

        $this->collection->add($resource->getRouteName($routeName), $route);
    }
}