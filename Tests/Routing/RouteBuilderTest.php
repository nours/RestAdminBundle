<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Routing;

use Nours\RestAdminBundle\Event\RestAdminEvents;
use Nours\RestAdminBundle\Event\RouteEvent;
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteBuilderTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RouteBuilderTest extends AdminTestCase
{
    /**
     * Checks that routes parameters can be overriden using events
     */
    public function testBuildRouteEvent()
    {
        $routes = new RouteCollection();
        $dispatcher = $this->getDispatcher();

        $builder = new RoutesBuilder($routes, $dispatcher);

        $dispatcher->addListener(RestAdminEvents::ROUTE, function(RouteEvent $event) {
            $event->options = array('test' => 'ok');
            $event->method  = 'POST';
        });

        $resource = $this->getAdminManager()->getResource('post');
        $routeName = 'test';
        $path      = 'path/to/test';

        $builder->addRoute($resource, $resource->getAction('get'), $routeName, 'GET', $path);

        $this->assertCount(1, $routes);

        $route = $routes->get($resource->getRouteName($routeName));

        $options = $route->getOptions();
        $this->assertArrayHasKey('test', $options);
        $this->assertEquals('ok', $options['test']);

        $this->assertEquals(array('POST'), $route->getMethods());
    }

    /**
     * @return EventDispatcherInterface
     */
    protected function getDispatcher()
    {
        return $this->get('event_dispatcher');
    }
}