<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Menu;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Matcher;
use Nours\RestAdminBundle\Menu\Helper\ResourceMenuHelper;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class MenuFunctionalTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class MenuFunctionalTest extends AdminTestCase
{
    /**
     * @var ResourceMenuHelper
     */
    private $helper;

    /**
     * @var ItemInterface
     */
    private $menu;

    /**
     * @var Matcher
     */
    private $matcher;

    public function setUp()
    {
        $this->helper  = new ResourceMenuHelper($this->getAdminManager());
        $this->matcher = $this->get('knp_menu.matcher');

        $this->menu = $this->get('knp_menu.factory')->createItem('root');
    }

    /**
     * @param $resourceName
     * @return ItemInterface
     */
    private function makeMenuItem($resourceName)
    {
        return $this->helper->createResourceMenuItem($this->menu, $resourceName, 'label.' . $resourceName);
    }

    /**
     * Request routes to post:index.
     *
     * Item is post resource, and should be both current and ancestor.
     */
    public function testPostIndexIsCurrentAndNotAncestor()
    {
        $item = $this->makeMenuItem('post');
        $stub = $item->getFirstChild();

        $this->initRequest('/posts', array(
            '_route' => 'post_index',
            'resource' => $this->getAdminManager()->getResource('post')
        ));

        // Stub item is current and not ancestor
        $this->assertTrue($this->matcher->isCurrent($stub));
        $this->assertFalse($this->matcher->isAncestor($stub));

        // Item is both current and ancestor
        $this->assertTrue($this->matcher->isCurrent($item));
        $this->assertTrue($this->matcher->isAncestor($item));
    }

    /**
     * Request routes to post:create
     *
     * Item is post resource, and should not be current but ancestor.
     */
    public function testPostCreateIsAncestor()
    {
        $item = $this->helper->createResourceMenuItem($this->menu, 'post', 'label.posts');
        $stub = $item->getFirstChild();

        $this->initRequest('/posts/create', array(
            '_route' => 'post_create',
            'resource' => $this->getAdminManager()->getResource('post')
        ));

        $this->assertTrue($this->matcher->isCurrent($stub));
        $this->assertTrue($this->matcher->isAncestor($item));
        $this->assertFalse($this->matcher->isCurrent($item));
    }

    /**
     * Request routes to post.comment:index
     *
     * Item is post.comment resource, and should be both current and ancestor
     */
    public function testPostCommentIndexIsCurrent()
    {
        $item = $this->helper->createResourceMenuItem($this->menu, 'post.comment', 'label.post.comments', array(
            'routeParameters' => array('post' => 1)
        ));
        $stub = $item->getFirstChild();

        $this->initRequest('/posts/1/comments', array(
            '_route' => 'post_comment_index',
            '_route_params' => array('post' => '1'),
            'resource' => $this->getAdminManager()->getResource('post.comment')
        ));

        // Stub item is current and not ancestor
        $this->assertTrue($this->matcher->isCurrent($stub));
        $this->assertFalse($this->matcher->isAncestor($stub));

        // Item is both current and ancestor
        $this->assertTrue($this->matcher->isAncestor($item));
        $this->assertTrue($this->matcher->isCurrent($item));
    }

    /**
     * Request routes to post.comment:create
     *
     * Item is post.comment, and should not be current but ancestor.
     */
    public function testPostCommentCreateIsAncestor()
    {
        $item = $this->helper->createResourceMenuItem($this->menu, 'post.comment', 'label.post.comments', array(
            'routeParameters' => array('post' => 1)
        ));
        $stub = $item->getFirstChild();

        $this->initRequest('/posts/1/comments/create', array(
            '_route' => 'post_comment_create',
            '_route_params' => array('post' => '1'),
            'resource' => $this->getAdminManager()->getResource('post.comment')
        ));

        $this->assertTrue($this->matcher->isCurrent($stub));
        $this->assertFalse($this->matcher->isAncestor($stub));

        $this->assertTrue($this->matcher->isAncestor($item));
        $this->assertFalse($this->matcher->isCurrent($item));
    }

    /**
     * Request routes to post.comment:index
     *
     * Menu has a post item having a comment child.
     *
     * Post item should be ancestor, comment item current.
     */
    public function testParentItemIsAncestor()
    {
        $postItem = $this->helper->createResourceMenuItem($this->menu, 'post', 'label.post');
        $postStub = $postItem->getFirstChild();

        $commentItem = $this->helper->createResourceMenuItem($postItem, 'post.comment', 'label.post.comments', array(
            'routeParameters' => array('post' => 1)
        ));

        $this->initRequest('/posts/1/comments', array(
            '_route' => 'post_comment_index',
            '_route_params' => array('post' => '1'),
            'resource' => $this->getAdminManager()->getResource('post.comment')
        ));

        // Comment item is current
        $this->assertTrue($this->matcher->isCurrent($commentItem));

        // Stub is nor current nor ancestor
        $this->assertFalse($this->matcher->isCurrent($postStub));
        $this->assertFalse($this->matcher->isAncestor($postStub));

        // Post item is ancestor but not current
        $this->assertFalse($this->matcher->isCurrent($postItem));
        $this->assertTrue($this->matcher->isAncestor($postItem));
    }


    private function initRequest($uri, array $attributes = array())
    {
        $request = Request::create($uri);
        $request->attributes->add($attributes);

        $this->get('request_stack')->push($request);
    }
}