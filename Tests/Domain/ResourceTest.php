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
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;

/**
 * Class ResourceTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceTest extends AdminTestCase
{
    /**
     * @var DomainResource
     */
    private $postResource;

    /**
     * @var DomainResource
     */
    private $commentResource;

    /**
     * @var DomainResource
     */
    private $commentBisResource;

    /**
     * @var DomainResource
     */
    private $postExtensionResource;

    protected function setUp(): void
    {
        $this->loadFixtures();

        $this->postResource    = $this->getAdminManager()->getResource('post');
        $this->commentResource = $this->getAdminManager()->getResource('post.comment');
        $this->commentBisResource = $this->getAdminManager()->getResource('post.comment_bis');
        $this->postExtensionResource = $this->getAdminManager()->getResource('post.extension');
    }


    public function testResourceCanHaveAnyConfig()
    {
        $this->assertEquals('bar', $this->commentResource->getConfig('foo'));
    }

    /**
     * The post resource has 3 children : comment, comment_bis and author
     */
    public function testGetChildren()
    {
        $children = $this->postResource->getChildren();

        $this->assertCount(3, $children);
        $this->assertTrue(in_array($this->commentResource, $children, true));
        $this->assertTrue(in_array($this->commentBisResource, $children, true));
        $this->assertTrue(in_array($this->postExtensionResource, $children, true));
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
    public function testGetRouteParamsForCollection()
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
    public function testGetBaseRouteParamsForTopLevelResource()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->postResource->getBaseRouteParams($post);

        $this->assertCount(0, $params);
    }

    /**
     * The resource route params for a top level instance should contain its id
     */
    public function testGetInstanceRouteParamsFromTopLevelResource()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->postResource->getInstanceRouteParams($post);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($post->getId(), $params['post']);
    }

    /**
     * The global route params for a resource having a parent should contain its parent identifier only.
     */
    public function testGetBaseRouteParamsOfChildResource()
    {
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $params = $this->commentResource->getBaseRouteParams($comment);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($comment->getPost()->getId(), $params['post']);
    }

    /**
     * The global route params for a resource having a parent should contain its parent identifier only.
     */
    public function testGetBaseRouteParamsOfParentResource()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->commentResource->getBaseRouteParams($post);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($post->getId(), $params['post']);
    }

    /**
     * The resource route params for a resource having a parent should contain the resource and it's parent identifiers.
     */
    public function testGetInstanceRouteParamsOfChildResource()
    {
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $params = $this->commentResource->getInstanceRouteParams($comment);

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
        $this->expectException('InvalidArgumentException');

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
        $this->assertEquals('extension', $this->postExtensionResource->getParamName());
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
    public function testGetInstanceRouteParamsOfCompositePKResource()
    {
        $composite = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'first',
        ));

        $compositeResource = $this->getAdminManager()->getResource('composite');

        $this->assertEquals(array(
            'composite_id' => 1,
            'composite_name' => 'first',
        ), $compositeResource->getInstanceRouteParams($composite));
    }

    /**
     */
    public function testGetRouteParamsForCollectionOfCompositePKResource()
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
            'id' => array(1, 1),
            'name' => array('first', 'second')
        ), $compositeResource->getCollectionRouteParams(array($composite, $composite2)));
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetRouteParamsMappingComposite()
    {
        $resource = $this->getAdminManager()->getResource('composite');

        $mappings = $resource->getRouteParamsMapping();

        $this->assertEquals(array(
            'composite_id'   => 'id',
            'composite_name' => 'name',
        ), $mappings);
    }

    /**
     * Prototype route parameters mappings
     */
    public function testGetRouteParamsMappingCompositeChild()
    {
        $resource = $this->getAdminManager()->getResource('composite.composite_child');

        $mappings = $resource->getRouteParamsMapping();

        $this->assertEquals(array(
            'composite_id' => 'parent.id',
            'composite_name' => 'parent.name',
            'composite_child_id' => 'id',
            'composite_child_name' => 'name',
        ), $mappings);
    }

    /**
     * Parent path
     */
    public function testGetParentPropertyPath()
    {
        $comment  = $this->getAdminManager()->getResource('post.comment');
        $response = $this->getAdminManager()->getResource('post.comment.comment_response');
        $child = $this->getAdminManager()->getResource('composite.composite_child');

        $this->assertEquals('post', $comment->getParentPropertyPath());
        $this->assertEquals('comment', $response->getParentPropertyPath());

        // Parent property path is defined explicitly in CompositeChildController
        $this->assertEquals('parent', $child->getParentPropertyPath());
    }

    /**
     * Parent path
     */
    public function testGetParentPropertyPathOfSingleResource()
    {
        $extension = $this->getAdminManager()->getResource('post.extension');

        $this->assertEquals('post', $extension->getParentPropertyPath());
    }

    /**
     * Child path for single resources
     */
    public function testGetSingleChildPath()
    {
        $post      = $this->getAdminManager()->getResource('post');
        $extension = $this->getAdminManager()->getResource('post.extension');

        $this->assertNull($post->getSingleChildPath());
        $this->assertEquals('extension', $extension->getSingleChildPath());
    }

    /**
     * Parent object from data
     */
    public function testGetParentObject()
    {
        $resource = $this->getAdminManager()->getResource('post.comment.comment_response');

        $data = $this->getEntityManager()->getRepository('FixtureBundle:CommentResponse')->find(1);

        $parent = $resource->getParentObject($data);

        $this->assertSame($data->getComment(), $parent);
        $this->assertEquals(1, $parent->getId());
    }

    /**
     * Parent object from data
     */
    public function testGetSingleChildObject()
    {
        $resource = $this->getAdminManager()->getResource('post.extension');

        /** @var Post $parent */
        $parent = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);

        $data = $resource->getSingleChildObject($parent);

        $this->assertSame($parent->getExtension(), $data);
        $this->assertEquals(1, $data->getId());
    }

    /**
     */
    public function testGetObjectIdentifiers()
    {
        $resource = $this->getAdminManager()->getResource('post.comment.comment_response');

        $data = $this->getEntityManager()->getRepository('FixtureBundle:CommentResponse')->find(1);

        $identifiers = $resource->getObjectIdentifiers($data);

        $this->assertEquals(array(
            'id' => 1
        ), $identifiers);
    }

    /**
     */
    public function testGetCompositeObjectIdentifiers()
    {
        $resource = $this->getAdminManager()->getResource('composite');

        $data = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'second'
        ));

        $identifiers = $resource->getObjectIdentifiers($data);

        $this->assertEquals(array(
            'id' => 1,
            'name' => 'second'
        ), $identifiers);
    }

    /**
     */
    public function testGetCompositeChildObjectIdentifiers()
    {
        $resource = $this->getAdminManager()->getResource('composite.composite_child');

        $data = $this->getEntityManager()->getRepository('FixtureBundle:CompositeChild')->findOneBy(array(
            'id' => 1
        ));

        $identifiers = $resource->getObjectIdentifiers($data);

        $this->assertEquals(array(
            'id' => 1,
            'name' => 'child'
        ), $identifiers);
    }

    /**
     *
     */
    public function testIsSingleResource()
    {
        $this->assertFalse($this->getAdminManager()->getResource('post')->isSingleResource());
        $this->assertTrue($this->getAdminManager()->getResource('post.extension')->isSingleResource());
    }

    /**
     * Action annotation can be used to disable default added index and get actions.
     */
    public function testDisableDefaultActionsUsingAnnotations()
    {
        $resource = $this->getAdminManager()->getResource('disabled_actions');

        $this->assertFalse($resource->hasAction('index'));
        $this->assertFalse($resource->hasAction('get'));
    }
}