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

    public function testRouteLoading()
    {
        $postIndex = $this->collection->get('post_index');

//        var_dump($this->collection->all());die;

        $this->assertNotNull($postIndex);
        $this->assertEquals('/posts.{_format}', $postIndex->getPath());
        $this->assertEquals('post', $postIndex->getDefault('_resource'));
        $this->assertEquals('index', $postIndex->getDefault('_action'));

        $postGet = $this->collection->get('post_get');

        $this->assertNotNull($postGet);
        $this->assertEquals('/posts/{post}.{_format}', $postGet->getPath());
        $this->assertEquals('post', $postGet->getDefault('_resource'));
        $this->assertEquals('get', $postGet->getDefault('_action'));

        $commentIndex = $this->collection->get('post_comment_index');

        $this->assertNotNull($commentIndex);
        $this->assertEquals('/posts/{post}/comments.{_format}', $commentIndex->getPath());
        $this->assertEquals('post.comment', $commentIndex->getDefault('_resource'));
        $this->assertEquals('index', $commentIndex->getDefault('_action'));

        $commentGet = $this->collection->get('post_comment_get');

        $this->assertNotNull($commentGet);
        $this->assertEquals('/posts/{post}/comments/{comment}.{_format}', $commentGet->getPath());
        $this->assertEquals('post.comment', $commentGet->getDefault('_resource'));
        $this->assertEquals('get', $commentGet->getDefault('_action'));

        $postCreate = $this->collection->get('post_create');

//        var_dump($this->collection->all());die;

        $this->assertNotNull($postCreate);
        $this->assertEquals('/posts.{_format}', $postCreate->getPath());
        $this->assertEquals('post', $postCreate->getDefault('_resource'));
        $this->assertEquals('create', $postCreate->getDefault('_action'));
        $this->assertEquals(array('POST'), $postCreate->getMethods());

        $postCreateForm = $this->collection->get('post_create_form');

//        var_dump($this->collection->all());die;

        $this->assertNotNull($postCreateForm);
        $this->assertEquals('/posts/new.{_format}', $postCreateForm->getPath());
        $this->assertEquals('post', $postCreateForm->getDefault('_resource'));
        $this->assertEquals('create', $postCreateForm->getDefault('_action'));
        $this->assertEquals(array('GET'), $postCreateForm->getMethods());

        $commentPublish = $this->collection->get('post_comment_publish_form');

        $this->assertNotNull($commentPublish);
        $this->assertEquals('/posts/{post}/comments/{comment}/publish.{_format}', $commentPublish->getPath());
        $this->assertEquals('post.comment', $commentPublish->getDefault('_resource'));
        $this->assertEquals('publish', $commentPublish->getDefault('_action'));
    }
}