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

/**
 * Class ResourceTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceTest extends AdminTestCase
{
    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $postResource;

    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $commentResource;

    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $commentBisResource;

    public function setUp()
    {
        $this->loadFixtures();

        $this->postResource = $this->getAdminManager()->getResource('post');
        $this->commentResource = $this->getAdminManager()->getResource('post.comment');
        $this->commentBisResource = $this->getAdminManager()->getResource('post.comment_bis');
    }


    public function testResourceCanHaveAnyConfig()
    {
        $this->assertEquals('bar', $this->commentResource->getConfig('foo'));
    }

    /**
     * The post resource has 2 children
     */
    public function testGetChildren()
    {
        $children = $this->postResource->getChildren();

        $this->assertCount(2, $children);
        $this->assertTrue(in_array($this->commentResource, $children, true));
        $this->assertTrue(in_array($this->commentBisResource, $children, true));
    }

    /**
     *
     */
    public function testGetChild()
    {
        $this->assertTrue($this->postResource->hasChild('comment'));

        $child = $this->postResource->getChild('comment');

        $this->assertSame($this->commentResource, $child);
    }

    /**
     * The global route params for a simple resource should always be empty
     */
    public function testGetCollectionRouteParams()
    {
        $post1 = $this->getEntityManager()->find('FixtureBundle:Post', 1);
        $post2 = $this->getEntityManager()->find('FixtureBundle:Post', 2);

        $params = $this->postResource->getCollectionRouteParams(array($post1, $post2));

        $this->assertEquals(array(
            'id' => array(1, 2)
        ), $params);
    }

    /**
     * The global route params for a simple resource should always be empty
     */
    public function testGetRouteParamsForTopLevelResource()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->postResource->getRouteParamsFromData($post);

        $this->assertCount(0, $params);
    }

    /**
     * The resource route params for a simple resource should contains it's id
     */
    public function testGetResourceRouteParamsForTopLevelResource()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->postResource->getResourceRouteParams($post);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($post->getId(), $params['post']);
    }

    /**
     * The global route params for a resource having a parent should contain it's parent identifier only.
     */
    public function testGetRouteParamsFromDataForResourceHavingParent()
    {
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $params = $this->commentResource->getRouteParamsFromData($comment);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($comment->getPost()->getId(), $params['post']);
    }

    /**
     * The global route params for a resource having a parent should contain it's parent identifier only.
     */
    public function testGetRouteParamsFromParentForResourceHavingParent()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->commentResource->getRouteParamsFromParent($post);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($post->getId(), $params['post']);
    }

    /**
     * The resource route params for a resource having a parent should contain the resource and it's parent identifiers.
     */
    public function testGetResourceRouteParamsForResourceHavingParent()
    {
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $params = $this->commentResource->getResourceRouteParams($comment);

        $this->assertCount(2, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertArrayHasKey('comment', $params);
        $this->assertEquals($comment->getPost()->getId(), $params['post']);
        $this->assertEquals($comment->getId(), $params['comment']);
    }

    /**
     * getAction throws
     */
    public function testGetActionThrowsIfActionIsUnknown()
    {
        $this->setExpectedException('InvalidArgumentException');

        $this->postResource->getAction('foo');
    }

    /**
     * The param name defaults to the name of the resource
     */
    public function testGetParamName()
    {
        $this->assertEquals('post', $this->postResource->getParamName());
        $this->assertEquals('comment', $this->commentResource->getParamName());
        $this->assertEquals('comment_bis', $this->commentBisResource->getParamName());
    }

    /**
     * Foo resource has custom fetchers :
     *  - uses annotation for index
     *  - uses fetcher param for get
     *
     * @see FooController
     */
    public function testParamFetcherAnnotation()
    {
        $fooResource = $this->getAdminManager()->getResource('foo');
        $index = $fooResource->getAction('index');
        $get   = $fooResource->getAction('get');

        $this->assertEquals('custom', $fooResource->getConfig('fetcher'));
        $this->assertEquals('custom', $index->getConfig('fetcher'));
        $this->assertEquals('foo', $get->getConfig('fetcher'));

        $controllerClass = 'Nours\RestAdminBundle\Tests\FixtureBundle\Controller\FooController';
        $this->assertEquals($controllerClass . '::fetchParamsDefault', $fooResource->getConfig('fetcher_callback'));
        $this->assertEquals($controllerClass . '::fetchParamsIndex', $index->getConfig('fetcher_callback'));
    }

    /**
     */
    public function testCompositeIdentifierEntity()
    {
        $compositeResource = $this->getAdminManager()->getResource('composite');

        $this->assertEquals(array('id', 'name'), $compositeResource->getIdentifier());
        $this->assertEquals(true, $compositeResource->isIdentifierComposite());
        $this->assertEquals('composite', $compositeResource->getParamName());
        $this->assertEquals(array(
            'id'   => 'composite_id',
            'name' => 'composite_name'
        ), $compositeResource->getIdentifierNames());
    }

    /**
     */
    public function testCompositeRouteParams()
    {
        $composite = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'first',
        ));
        $composite2 = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'second',
        ));

        $compositeResource = $this->getAdminManager()->getResource('composite');

        $this->assertEquals(array(
            'composite_id' => 1,
            'composite_name' => 'first',
        ), $compositeResource->getResourceRouteParams($composite));

        $this->assertEquals(array(
            'id' => array(1, 1),
            'name' => array('first', 'second')
        ), $compositeResource->getCollectionRouteParams(array($composite, $composite2)));
    }
}