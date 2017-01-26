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

    public function setUp()
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
        $this->assertSame('tests.controller.comment:copyComment', $action->getFactory());

        // Copy action is read only
        $this->assertTrue($action->isReadOnly());
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
}