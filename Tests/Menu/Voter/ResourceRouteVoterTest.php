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

    public function setUp()
    {
        parent::setUp();

        $this->voter = $this->get('rest_admin.menu.voter');
    }

    /**
     * Request must be set to use matcher.
     */
    public function testMatchItemThrowsIfNoRequest()
    {
        $this->setExpectedException('DomainException');

        $this->voter->matchItem($this->createItem());
    }

    /**
     * Matches a request without resource
     */
    public function testMatchItemReturnsNullIfNoResource()
    {
        $this->initRequest();

        $this->assertNull($this->voter->matchItem($this->createItem()));
    }


    public function testMatchesWithIndexRoute()
    {
        $this->initRequest(array(
            'resource' => $this->getAdminManager()->getResource('post'),
            '_route' => 'post_index'
        ));

        $item = $this->createItem('post_index');

        $this->assertTrue($this->voter->matchItem($item));
    }


    public function testMatchesWithEditRoute()
    {
        $this->initRequest(array(
            'resource' => $this->getAdminManager()->getResource('post'),
            '_route' => 'post_edit'
        ));

        $item = $this->createItem('post_edit');

        $this->assertTrue($this->voter->matchItem($item));
    }


    public function testDoesntMatchWithParentResource()
    {
        $this->initRequest(array(
            'resource' => $this->getAdminManager()->getResource('post'),
            '_route' => 'post_edit'
        ));

        $item = $this->createItem('post_comment_edit');

        $this->assertFalse($this->voter->matchItem($item));
    }


    public function testReturnsNullIfRouteNameDoNotMatch()
    {
        $this->initRequest(array(
            'resource' => $this->getAdminManager()->getResource('post'),
            '_route' => 'foobar'
        ));

        $item = $this->createItem('post_index');

        $this->assertNull($this->voter->matchItem($item));
    }


    /**
     * @return MenuItem
     */
    private function createItem($route = null)
    {
        $item = new MenuItem('item', new MenuFactory());

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

        $this->voter->setRequest($request);
    }
}