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

use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;

/**
 * Class ActionTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionTest extends AdminTestCase
{
    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
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
    public function testGetPrototypeParamsMapping()
    {
        $action = $this->getAdminManager()->getAction('post.comment_bis:edit');

        $mappings = $action->getPrototypeParamsMapping();

        $this->assertEquals(array(
            '__post__' => 'post.id',
            '__comment_bis__' => 'id'
        ), $mappings);
    }
}