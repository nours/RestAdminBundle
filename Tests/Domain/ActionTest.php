<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Domain;

use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class ActionTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionTest extends AdminTestCase
{
    /**
     * @var DomainResource
     */
    private $resource;

    protected function setUp(): void
    {
        $this->resource = $this->getAdminManager()->getResource('post');
    }

    public function testIndexAction()
    {
        $action = $this->resource->getAction('index');

        $this->assertSame($this->resource, $action->getResource());

        // Extra action param
        $this->assertSame('list', $action->getConfig('icon'));

        // Index action is read only
        $this->assertTrue($action->isReadOnly());
    }

    public function testCopyAction()
    {
        $action = $this->getAdminManager()->getAction('post.comment:copy');

        // Factory
        $this->assertSame('tests.controller.comment::copyComment', $action->getFactory());

        // Copy action is NOT read only
        $this->assertFalse($action->isReadOnly());
    }

    public function testGetRouteName()
    {
        $action = $this->resource->getAction('index');

        $this->assertEquals($this->resource->getRouteName('index'), $action->getRouteName());
    }

    public function testGetActionIsReadOnly()
    {
        $action = $this->resource->getAction('get');

        $this->assertTrue($action->isReadOnly());
    }

    /**
     * @dataProvider getNotReadOnlyActionNames
     *
     * @param $actionName
     */
    public function testNotReadOnlyActions($actionName)
    {
        $action = $this->resource->getAction($actionName);

        $this->assertFalse($action->isReadOnly());
    }

    /**
     * The actions which do have treatments
     *
     * @return array
     */
    public function getNotReadOnlyActionNames()
    {
        return array(
            array('create'),
            array('edit'),
            array('delete'),
            array('bulk_delete'),
            array('custom_form'),
        );
    }


    public function testCustomActionReadOnly()
    {
        // CommentBis resource has custom actions (test and other)
        $resource = $this->getAdminManager()->getResource('post.comment_bis');

        $action = $resource->getAction('test');
        $this->assertTrue($action->isReadOnly());

        $action = $resource->getAction('other');
        $this->assertFalse($action->isReadOnly());

        // And also
        $action = $resource->getAction('test_default_route');
        $this->assertFalse($action->isReadOnly());

        $action = $resource->getAction('test_default_route_global');
        $this->assertFalse($action->isReadOnly());
    }


    public function testGetRouteParamsForParent()
    {
        $this->loadFixtures();

        $action = $this->getAdminManager()->getAction('post.comment_bis:create');
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $action->getRouteParams($post);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals(1, $params['post']);
    }

    public function testGetRouteParamsForInstance()
    {
        $this->loadFixtures();

        $action = $this->getAdminManager()->getAction('post.comment_bis:edit');
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $params = $action->getRouteParams($comment);

        $this->assertCount(2, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals(1, $params['post']);
        $this->assertArrayHasKey('comment_bis', $params);
        $this->assertEquals(1, $params['comment_bis']);
    }

    public function testGetRouteParamsForBulk()
    {
        $this->loadFixtures();

        $action = $this->getAdminManager()->getAction('post:bulk_delete');
        $post1 = $this->getEntityManager()->find('FixtureBundle:Post', 1);
        $post2 = $this->getEntityManager()->find('FixtureBundle:Post', 2);

        // Using some instances
        $params = $action->getRouteParams(array($post1, $post2));
        $this->assertEquals([
            'id' => array(1, 2)
        ], $params);

        // Without instances
        $params = $action->getRouteParams();
        $this->assertCount(0, $params);
    }

    public function testGetRouteParamsForBulkUsingParent()
    {
        $this->loadFixtures();

        $action = $this->getAdminManager()->getAction('post.comment:bulk_delete');
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        // Without instances
        $params = $action->getRouteParams($post);
        $this->assertEquals([
            'post' => 1
        ], $params);
    }

    public function testGetRouteParamsForSingleResource()
    {
        $this->loadFixtures();

        $action = $this->getAdminManager()->getAction('post.extension:create');
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $action->getRouteParams($post);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals(1, $params['post']);
    }

    public function testGetRouteParamsForSingleResourceWithInstance()
    {
        $this->loadFixtures();

        $action = $this->getAdminManager()->getAction('post.extension:edit');
        $extension = $this->getEntityManager()->find('FixtureBundle:PostExtension', 1);

        $params = $action->getRouteParams($extension);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals(1, $params['post']);

        // Works also with parent instance
        $params = $action->getRouteParams($extension->getPost());

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals(1, $params['post']);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMappingForPostIndex()
    {
        $action = $this->getAdminManager()->getAction('post:index');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMappingForPostEdit()
    {
        $action = $this->getAdminManager()->getAction('post:edit');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__post__' => 'id'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMappingForPostCopy()
    {
        $action = $this->getAdminManager()->getAction('post:copy');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__post__' => 'id'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMappingForPostCommentIndex()
    {
        $action = $this->getAdminManager()->getAction('post.comment:index');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__post__' => 'id'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMapping()
    {
        $action = $this->getAdminManager()->getAction('post.comment_bis:edit');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__post__' => 'post.id',
            '__comment_bis__' => 'id'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParamsForPostIndex()
    {
        $action = $this->getAdminManager()->getAction('post:index');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParamsForPostEdit()
    {
        $action = $this->getAdminManager()->getAction('post:edit');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(
            'post' => '__post__'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParamsForPostCopy()
    {
        $action = $this->getAdminManager()->getAction('post:copy');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(
            'post' => '__post__'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParamsForPostCommentIndex()
    {
        $action = $this->getAdminManager()->getAction('post.comment:index');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(
            'post' => '__post__'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParams()
    {
        $action = $this->getAdminManager()->getAction('post.comment_bis:edit');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(
            'post' => '__post__',
            'comment_bis' => '__comment_bis__'
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMappingComposite()
    {
        $action = $this->getAdminManager()->getAction('composite:edit');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__composite_id__' => 'id',
            '__composite_name__' => 'name',
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeParamsMappingCompositeChild()
    {
        $action = $this->getAdminManager()->getAction('composite.composite_child:get');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__composite_id__'         => 'parent.id',
            '__composite_name__'       => 'parent.name',
            '__composite_child_id__'   => 'id',
            '__composite_child_name__' => 'name',
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParamsComposite()
    {
        $action = $this->getAdminManager()->getAction('composite:edit');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(
            'composite_id' => '__composite_id__',
            'composite_name' => '__composite_name__',
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetPrototypeRouteParamsCompositeChild()
    {
        $action = $this->getAdminManager()->getAction('composite.composite_child:get');

        $mappings = $action->getPrototypeRouteParams();

        $this->assertEquals(array(
            'composite_id' => '__composite_id__',
            'composite_name' => '__composite_name__',
            'composite_child_id' => '__composite_child_id__',
            'composite_child_name' => '__composite_child_name__',
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetOrmAction()
    {
        $resource = $this->getAdminManager()->getResource('post');

        $this->assertNull($resource->getAction('index')->getConfig('handler_action'));
        $this->assertNull($resource->getAction('get')->getConfig('handler_action'));
        $this->assertEquals('create', $resource->getAction('create')->getConfig('handler_action'));
        $this->assertEquals('update', $resource->getAction('edit')->getConfig('handler_action'));
        $this->assertEquals('delete', $resource->getAction('delete')->getConfig('handler_action'));
        $this->assertEquals('delete', $resource->getAction('bulk_delete')->getConfig('handler_action'));
        $this->assertEquals('update', $resource->getAction('custom_form')->getConfig('handler_action'));
    }

    /**
     * Actions options defaults can be set using extras:defaults on main application config files.
     *
     * @see Tests/app/config/config_test.yml
     */
    public function testActionDefaultConfiguration()
    {
        $this->assertEquals('baz', $this->getAdminManager()->getAction('post:index')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post:get')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post:create')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post:edit')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post:delete')->getConfig('default_option'));

        $this->assertEquals('baz', $this->getAdminManager()->getAction('post.comment:index')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post.comment:get')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post.comment:create')->getConfig('default_option'));
        $this->assertEquals('foobar', $this->getAdminManager()->getAction('post.comment:edit')->getConfig('default_option'));
    }

    /**
     * Single resource actions.
     */
    public function testSingleResourceActions()
    {
        $resource = $this->getAdminManager()->getResource('post.extension');

        // 4 Actions for resource author : get, create, edit and delete
        // Index is not added by default as resource is single
        $this->assertCount(4, $resource->getActions());
        $this->assertTrue($resource->hasAction('get'));
        $this->assertTrue($resource->hasAction('create'));
        $this->assertTrue($resource->hasAction('edit'));
        $this->assertTrue($resource->hasAction('delete'));
    }

    public function testGetBaseUri()
    {
        $postResource = $this->getAdminManager()->getResource('post');

        // Suffix is empty, otherwise defaults to index
        $this->assertEquals('posts', $postResource->getAction('index')->getUriPath(''));

        // Suffix is empty, otherwise defaults to get
        $this->assertEquals('posts/{post}', $postResource->getAction('get')->getUriPath(''));

        // Create has no instance : no resource param
        $this->assertEquals('posts/create', $postResource->getAction('create')->getUriPath());

        $this->assertEquals('posts/{post}/edit', $postResource->getAction('edit')->getUriPath());
        $this->assertEquals('posts/{post}/delete', $postResource->getAction('delete')->getUriPath());
    }

    /**
     * Single resources have no identifiers in their urls, as they are tied to their parents.
     */
    public function testGetBaseUriForSingleResource()
    {
        $extensionResource = $this->getAdminManager()->getResource('post.extension');

        // Suffix is empty, otherwise defaults to get
        $this->assertEquals('posts/{post}/extension', $extensionResource->getAction('get')->getUriPath(''));

        // No id param in urls
        $this->assertEquals('posts/{post}/extension/create', $extensionResource->getAction('create')->getUriPath());
        $this->assertEquals('posts/{post}/extension/edit', $extensionResource->getAction('edit')->getUriPath());
        $this->assertEquals('posts/{post}/extension/delete', $extensionResource->getAction('delete')->getUriPath());
    }

    public function testGetFormActionRouteSuffix()
    {
        $resource = $this->getAdminManager()->getResource('post');

        $this->assertEquals('new', $resource->getAction('create')->getFormActionRouteSuffix());
        $this->assertEquals('update', $resource->getAction('edit')->getFormActionRouteSuffix());
        $this->assertEquals('remove', $resource->getAction('delete')->getFormActionRouteSuffix());
        $this->assertEquals('copy', $resource->getAction('copy')->getFormActionRouteSuffix());
        $this->assertEquals('bulk_remove', $resource->getAction('bulk_delete')->getFormActionRouteSuffix());
    }

    public function testGetFormActionRouteName()
    {
        $resource = $this->getAdminManager()->getResource('post');

        $this->assertEquals('post_new', $resource->getAction('create')->getFormActionRouteName());
        $this->assertEquals('post_update', $resource->getAction('edit')->getFormActionRouteName());
        $this->assertEquals('post_remove', $resource->getAction('delete')->getFormActionRouteName());
        $this->assertEquals('post_copy', $resource->getAction('copy')->getFormActionRouteName());
        $this->assertEquals('post_bulk_remove', $resource->getAction('bulk_delete')->getFormActionRouteName());
    }
}