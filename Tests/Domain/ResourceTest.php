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

    public function setUp()
    {
        $this->loadFixtures();

        $this->postResource = $this->getAdminManager()->getResource('post');
        $this->commentResource = $this->getAdminManager()->getResource('post.comment');
    }

    /**
     * The global route params for a simple resource should always be empty
     */
    public function testGetRouteParamsForTopLevelResource()
    {
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        $params = $this->postResource->getRouteParams($post);

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
    public function testGetRouteParamsForResourceHavingParent()
    {
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $params = $this->commentResource->getRouteParams($comment);

        $this->assertCount(1, $params);
        $this->assertArrayHasKey('post', $params);
        $this->assertEquals($comment->getPost()->getId(), $params['post']);
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
}