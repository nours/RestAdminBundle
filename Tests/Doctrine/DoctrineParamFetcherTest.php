<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Doctrine;

use Nours\RestAdminBundle\ParamFetcher\DoctrineParamFetcher;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Composite;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RepositoryTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineParamFetcherTest extends AdminTestCase
{
    /**
     * @var DoctrineParamFetcher
     */
    private $fetcher;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->fetcher = new DoctrineParamFetcher($this->getEntityManager());
    }

    /**
     * Simple use case : the resource has one parameter.
     */
    public function testFindModel()
    {
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->findOneBy(array());

        $resource = $this->getAdminManager()->getResource('post');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'post' => $post->getId()
        ));

        $this->fetcher->fetch($request);

        $found = $request->attributes->get('data');

        $this->assertSame($post, $found);
    }

    /**
     * Hierarchy use case : the resource and it's parent must be loaded.
     */
    public function testFindModelHierarchy()
    {
        $comment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->findOneBy(array());
        $post = $comment->getPost();

        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'comment' => $comment->getId(),
            'post' => $post->getId()
        ));

        $this->fetcher->fetch($request);

        $found = $request->attributes->get('data');

        $this->assertSame($comment, $found);
    }

    /**
     * Parent only use case : the resource has only it's parent parameter.
     */
    public function testFindParentModel()
    {
        $comment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->findOneBy(array());
        $post = $comment->getPost();

        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('index'),
            'post' => $post->getId()
        ));

        $this->fetcher->fetch($request);

        $found = $request->attributes->get('parent');

        $this->assertSame($post, $found);
    }

    /**
     * Parent only use case : the resource has only it's parent parameter.
     */
    public function testFindParentModelThrowsIfParentDoNotMatch()
    {
        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'post' => 2,
            'comment' => 1,
        ));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $this->fetcher->fetch($request);
    }

    /**
     * Searches a collection of posts (ids 1 and 2)
     */
    public function testFindCollection()
    {
        $resource = $this->getAdminManager()->getResource('post');

        $request = new Request(array('id' => array(1, 2)), array(), array(
            'resource' => $resource,
            'action'   => $resource->getAction('bulk_delete')
        ));

        $this->fetcher->fetch($request);

        $data = $request->attributes->get('data');

        $this->assertInternalType('array', $data);
        $this->assertCount(2, $data);

        $this->assertInstanceOf('Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post', $data[0]);
        $this->assertInstanceOf('Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post', $data[1]);
        $this->assertEquals(1, $data[0]->getId());
        $this->assertEquals(2, $data[1]->getId());
    }

    /**
     * Searches a collection of comments
     */
    public function testFindChildrenCollection()
    {
        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request(array('id' => 1), array(), array(
            'resource' => $resource,
            'post' => 1     // The comments are loaded from post 1
        ));

        $this->fetcher->fetch($request);

        $data = $request->attributes->get('data');

        $this->assertInternalType('array', $data);
        $this->assertCount(1, $data);

        $this->assertInstanceOf('Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment', $data[0]);
        $this->assertEquals(1, $data[0]->getId());
    }

    /**
     * If the collection result is empty, throws
     */
    public function testFindCollectionThrowsIfCollectionEmpty()
    {
        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request(array('id' => array()), array(), array(
            'resource' => $resource,
            'post' => 1
        ));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $this->fetcher->fetch($request);
    }

    /**
     * If a resource id is not found, it throws
     */
    public function testFindCollectionThrowsIfAnyEntityIsNotFound()
    {
        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request(array('id' => array(1, 9999)), array(), array(
            'resource' => $resource,
            'post' => 1
        ));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $this->fetcher->fetch($request);
    }

    /**
     * If the parent entity cannot be retrieved, it throws
     */
    public function testFindCollectionThrowsIfParentNotFound()
    {
        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request(array('id' => 1), array(), array(
            'resource' => $resource,
            'post' => 9999
        ));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $this->fetcher->fetch($request);
    }

    /**
     * If the parent entity is not the parent of the resources fetched, it throws
     */
    public function testFindCollectionThrowsIfParentDoNotMatch()
    {
        $resource = $this->getAdminManager()->getResource('post.comment');

        $request = new Request(array('id' => 1), array(), array(
            'resource' => $resource,
            'post' => 2
        ));

        $this->setExpectedException('Symfony\Component\HttpKernel\Exception\NotFoundHttpException');

        $this->fetcher->fetch($request);
    }

    /**
     * If the parent entity is not the parent of the resources fetched, it throws
     */
    public function testFindCompositeIdentifier()
    {
        $resource = $this->getAdminManager()->getResource('composite');

        $request = new Request(array(), array(), array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'composite_id' => 1,
            'composite_name' => 'first'
        ));

        $this->fetcher->fetch($request);

        $this->assertTrue($request->attributes->has('data'));

        /** @var Composite $composite */
        $composite = $request->attributes->get('data');

        $this->assertInstanceOf('Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Composite', $composite);
        $this->assertEquals(1, $composite->getId());
        $this->assertEquals('first', $composite->getName());
    }
}