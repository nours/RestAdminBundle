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

use Nours\RestAdminBundle\Api\KernelProvider;
use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Nours\RestAdminBundle\Domain\Resource;


/**
 * Class RoutesBuilder
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RoutesBuilder
{
    private $collection;

    /**
     * @param RouteCollection $collection
     */
    public function __construct(RouteCollection $collection)
    {
        $this->collection = $collection;
    }


    public function addRoute(Resource $resource, Action $action, $routeName, $method, $path)
    {
        $defaults = array(
            '_resource'   => $resource->getFullName(),
            '_action'     => $action->getName(),
            '_controller' => $action->getController(),
            '_format'     => null
        );

        $reqs = $options = array();
        $route = new Route(
            $path . '.{_format}', $defaults, $reqs, $options, '', array(), array($method)
        );

        $this->collection->add($resource->getRouteName($routeName), $route);
    }
}