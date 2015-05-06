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
    private $subject;

    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures();

        $this->subject = new DoctrineParamFetcher($this->getEntityManager());
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
            '_resource' => $resource,
            '_action' => $resource->getAction('get'),
            'post' => $post->getId()
        ));

        $this->subject->fetchParams($request);

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
            '_resource' => $resource,
            '_action' => $resource->getAction('get'),
            'comment' => $comment->getId(),
            'post' => $post->getId()
        ));

        $this->subject->fetchParams($request);

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
            '_resource' => $resource,
            '_action' => $resource->getAction('index'),
            'post' => $post->getId()
        ));

        $this->subject->fetchParams($request);

        $found = $request->attributes->get('parent');

        $this->assertSame($post, $found);
    }
}