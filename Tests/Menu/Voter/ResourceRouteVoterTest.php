<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Menu\Voter;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Nours\RestAdminBundle\Menu\Voter\ResourceRouteVoter;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RestResourceRouteVoterTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceRouteVoterTest extends AdminTestCase
{
    /**
     * @var ResourceRouteVoter
     */
    private $voter;

    /**
     * @var RequestStack
     */
    private $requestStack;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStack = new RequestStack();
        $this->voter = new ResourceRouteVoter($this->requestStack);
    }

    /**
     * The request has no resource
     */
    public function testMatchItemReturnsNullIfNoResource()
    {
        $this->initRequest();

        $item = $this->createItem('post_edit', array(
            'resource' => $this->getAdminManager()->getResource('post')
        ));

        $this->assertNull($this->voter->matchItem($item));
    }

    /**
     * The request has a resource, but the item doesn't
     */
    public function testReturnsNullIfItemHasNoResource()
    {
        $this->initRequest(array(
            'resource' => $this->getAdminManager()->getResource('post'),
            '_route' => 'post_index'
        ));

        // The item as no resource extra
        $item = $this->createItem('post_index');

        $this->assertNull($this->voter->matchItem($item));
    }

    /**
     * The request has a resource, but the item doesn't
     */
    public function testReturnsNullIfRequestHasNoResource()
    {
        $this->initRequest(array(
            '_route' => 'post_index'
        ));

        // The item as no resource extra
        $item = $this->createItem('post_index', array(
            'resource' => $this->getAdminManager()->getResource('post')
        ));

        $this->assertNull($this->voter->matchItem($item));
    }

    /**
     * The resource of the item matches the request
     */
    public function testMatchItemWithSameResource()
    {
        $resource = $this->getAdminManager()->getResource('post');

        $this->initRequest(array(
            'resource' => $resource,
            '_route' => 'post_edit'
        ));

        $item = $this->createItem('post_edit', array(
            'resource' => $resource
        ));

        $this->assertTrue($this->voter->matchItem($item));
    }

    /**
     * The resource of the item does not match the request.
     */
    public function testDoNotMatchItemWithOtherResource()
    {
        $this->initRequest(array(
            'resource' => $this->getAdminManager()->getResource('post'),
            '_route' => 'post_edit'
        ));

        $item = $this->createItem('post_edit', array(
            'resource' => $this->getAdminManager()->getResource('post.comment')
        ));

        $this->assertFalse($this->voter->matchItem($item));
    }

    /**
     * @param null $route
     * @param array $extras
     * @return MenuItem
     */
    private function createItem($route = null, array $extras = array())
    {
        $item = new MenuItem('item', new MenuFactory());
        $item->setExtras($extras);

        if ($route) {
            $item->setExtra('routes', array(array('route' => $route)));
        }

        return $item;
    }

    /**
     * @return Request
     */
    private function initRequest(array $attributes = array())
    {
        $request = Request::create('test');
        $request->attributes->add($attributes);

        $this->requestStack->push($request);
    }
}