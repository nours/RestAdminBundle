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

use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RoutingLoaderTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RoutingLoaderTest extends AdminTestCase
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var RouteCollection
     */
    private $collection;

    public function setUp()
    {
        parent::setUp();

        $this->router = $this->get('router');

        $this->collection = $this->router->getRouteCollection();
    }

    public function testPostControllerRouteLoading()
    {
        // Index route
        $postIndex = $this->collection->get('post_index');

        $this->assertNotNull($postIndex);
        $this->assertEquals('/posts.{_format}', $postIndex->getPath());
        $this->assertEquals('post', $postIndex->getDefault('_resource'));
        $this->assertEquals('index', $postIndex->getDefault('_action'));

        // Get route
        $postGet = $this->collection->get('post_get');

        $this->assertNotNull($postGet);
        $this->assertEquals('/posts/{post}.{_format}', $postGet->getPath());
        $this->assertEquals('post', $postGet->getDefault('_resource'));
        $this->assertEquals('get', $postGet->getDefault('_action'));

        // Create form
        $postCreateForm = $this->collection->get('post_create');

        $this->assertNotNull($postCreateForm);
        $this->assertEquals('/posts/create.{_format}', $postCreateForm->getPath());
        $this->assertEquals('post', $postCreateForm->getDefault('_resource'));
        $this->assertEquals('create', $postCreateForm->getDefault('_action'));
        $this->assertEquals(array('GET'), $postCreateForm->getMethods());

        // New post
        $postCreate = $this->collection->get('post_new');

        $this->assertNotNull($postCreate);
        $this->assertEquals('/posts.{_format}', $postCreate->getPath());
        $this->assertEquals('post', $postCreate->getDefault('_resource'));
        $this->assertEquals('create', $postCreate->getDefault('_action'));
        $this->assertEquals(array('POST'), $postCreate->getMethods());

        // Edit form
        $postCreateForm = $this->collection->get('post_edit');

        $this->assertNotNull($postCreateForm);
        $this->assertEquals('/posts/{post}/edit.{_format}', $postCreateForm->getPath());
        $this->assertEquals('post', $postCreateForm->getDefault('_resource'));
        $this->assertEquals('edit', $postCreateForm->getDefault('_action'));
        $this->assertEquals(array('GET'), $postCreateForm->getMethods());

        // Update post
        $postCreate = $this->collection->get('post_update');

        $this->assertNotNull($postCreate);
        $this->assertEquals('/posts/{post}.{_format}', $postCreate->getPath());
        $this->assertEquals('post', $postCreate->getDefault('_resource'));
        $this->assertEquals('edit', $postCreate->getDefault('_action'));
        $this->assertEquals(array('PUT'), $postCreate->getMethods());

        // Delete form
        $postCreateForm = $this->collection->get('post_delete');

        $this->assertNotNull($postCreateForm);
        $this->assertEquals('/posts/{post}/delete.{_format}', $postCreateForm->getPath());
        $this->assertEquals('post', $postCreateForm->getDefault('_resource'));
        $this->assertEquals('delete', $postCreateForm->getDefault('_action'));
        $this->assertEquals(array('GET'), $postCreateForm->getMethods());

        // Remove post
        $postCreate = $this->collection->get('post_remove');

        $this->assertNotNull($postCreate);
        $this->assertEquals('/posts/{post}.{_format}', $postCreate->getPath());
        $this->assertEquals('post', $postCreate->getDefault('_resource'));
        $this->assertEquals('delete', $postCreate->getDefault('_action'));
        $this->assertEquals(array('DELETE'), $postCreate->getMethods());
    }

    public function testCommentControllerRouteLoading()
    {
        // Comment index
        $commentIndex = $this->collection->get('post_comment_index');

        $this->assertNotNull($commentIndex);
        $this->assertEquals('/posts/{post}/comments.{_format}', $commentIndex->getPath());
        $this->assertEquals('post.comment', $commentIndex->getDefault('_resource'));
        $this->assertEquals('index', $commentIndex->getDefault('_action'));
        $this->assertEquals(array('GET'), $commentIndex->getMethods());

        // Comment get
        $commentGet = $this->collection->get('post_comment_get');

        $this->assertNotNull($commentGet);
        $this->assertEquals('/posts/{post}/comments/{comment}.{_format}', $commentGet->getPath());
        $this->assertEquals('post.comment', $commentGet->getDefault('_resource'));
        $this->assertEquals('get', $commentGet->getDefault('_action'));
        $this->assertEquals(array('GET'), $commentGet->getMethods());

        // Comment publish
        $commentPublish = $this->collection->get('post_comment_publish_form');

        $this->assertNotNull($commentPublish);
        $this->assertEquals('/posts/{post}/comments/{comment}/publish.{_format}', $commentPublish->getPath());
        $this->assertEquals('post.comment', $commentPublish->getDefault('_resource'));
        $this->assertEquals('publish', $commentPublish->getDefault('_action'));
    }

    public function testCommentBisControllerRouteLoading()
    {
        // Comment index
        $route = $this->collection->get('post_commentbis_index');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('index', $route->getDefault('_action'));
        $this->assertEquals(array('GET'), $route->getMethods());

        // Comment get
        $route = $this->collection->get('post_commentbis_get');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/{commentbis}.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('get', $route->getDefault('_action'));
        $this->assertEquals(array('GET'), $route->getMethods());

        // Comment create
        $route = $this->collection->get('post_commentbis_create');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/create.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('create', $route->getDefault('_action'));
        $this->assertEquals(array('GET'), $route->getMethods());

        // Comment new
        $route = $this->collection->get('post_commentbis_new');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('create', $route->getDefault('_action'));
        $this->assertEquals(array('POST'), $route->getMethods());

        // Comment edit
        $route = $this->collection->get('post_commentbis_edit');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/{commentbis}/edit.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('edit', $route->getDefault('_action'));
        $this->assertEquals(array('GET'), $route->getMethods());

        // Comment new
        $route = $this->collection->get('post_commentbis_update');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/{commentbis}.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('edit', $route->getDefault('_action'));
        $this->assertEquals(array('PUT'), $route->getMethods());

        // Comment test action
        $route = $this->collection->get('post_commentbis_test');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/test.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('test', $route->getDefault('_action'));
        $this->assertEquals(array('GET'), $route->getMethods());

        // Comment other action
        $route = $this->collection->get('post_commentbis_other');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/{commentbis}/test.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('other', $route->getDefault('_action'));
        $this->assertEquals(array('GET'), $route->getMethods());

        $route = $this->collection->get('post_commentbis_other_do');

        $this->assertNotNull($route);
        $this->assertEquals('/posts/{post}/commentbis/{commentbis}/test.{_format}', $route->getPath());
        $this->assertEquals('post.commentbis', $route->getDefault('_resource'));
        $this->assertEquals('other', $route->getDefault('_action'));
        $this->assertEquals(array('POST'), $route->getMethods());
    }
}