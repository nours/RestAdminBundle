<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Menu\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResourceRouteVoter.
 *
 * Matches an item using a resource index route.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceRouteVoter implements VoterInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param ItemInterface $item
     * @return null
     */
    public function matchItem(ItemInterface $item)
    {
        if ($item->getExtra('disable_resource_voter')) {
            return null;
        }

        if (empty($this->request)) {
            throw new \DomainException("No request for matching item against");
        }

        /** @var \Nours\RestAdminBundle\Domain\Resource $resource */
        $resource = $this->request->attributes->get('resource');
        if (empty($resource)) {
            return null;
        }

        $route = $this->request->attributes->get('_route');

        $routePrefix    = $resource->getRoutePrefix();
        $routePrefixLen = strlen($routePrefix);

        // Check that current route matches the prefix
        if (substr($route, 0, $routePrefixLen) != $routePrefix) {
            return null;
        }

        $routes = (array) $item->getExtra('routes', array());
        foreach ($routes as $route) {
            $routeName = $route['route'];

            // Check that the route name matches the resource's prefix
            if (substr($routeName, 0, $routePrefixLen) == $routePrefix) {

                // Then extract the action's name
                $actionName = substr($routeName, $routePrefixLen);

                return $resource->hasAction($actionName);
            }
        }
    }
}