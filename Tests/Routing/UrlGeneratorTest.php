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

use Nours\RestAdminBundle\Routing\UrlGenerator;
use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class UrlGeneratorTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class UrlGeneratorTest extends AdminTestCase
{
    /**
     * @var UrlGenerator
     */
    private $generator;

    public function setUp()
    {
        parent::setUp();

        $this->generator = $this->get('rest_admin.routing.url_generator');
    }

    /**
     * Generate root resource index
     */
    public function testGenerateRootIndex()
    {
        $action = $this->getAction('post', 'index');

        $url = $this->generator->generate($action);

        $this->assertEquals('/posts', $url);
    }

    /**
     * Generate root resource get
     */
    public function testGenerateRootGet()
    {
        $action = $this->getAction('post', 'get');
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 2);

        $url = $this->generator->generate($action, array(
            'data' => $post
        ));

        $this->assertEquals('/posts/2', $url);
    }

    /**
     * Generate root resource get
     */
    public function testGenerateRootCreate()
    {
        $action = $this->getAction('post', 'create');

        $url = $this->generator->generate($action);

        $this->assertEquals('/posts/create', $url);
    }

    /**
     * Generate root resource get
     */
    public function testGenerateChildIndex()
    {
        $action = $this->getAction('post.comment', 'index');
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 2);

        $url = $this->generator->generate($action, array(
            'parent' => $post
        ));

        $this->assertEquals('/posts/2/comments', $url);
    }

    /**
     * Generate root resource get
     */
    public function testGenerateChildGet()
    {
        $action = $this->getAction('post.comment', 'get');
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $url = $this->generator->generate($action, array(
            'data' => $comment
        ));

        $this->assertEquals('/posts/1/comments/1', $url);
    }

    /**
     * Generate root resource get
     */
    public function testGenerateChildCreate()
    {
        $action = $this->getAction('post.comment', 'create');
        $comment = $this->getEntityManager()->find('FixtureBundle:Comment', 1);

        $url = $this->generator->generate($action, array(
            'parent' => $comment
        ));

        $this->assertEquals('/posts/1/comments/create', $url);
    }


    private function getAction($resourceName, $name)
    {
        return $this->getAdminManager()->getResource($resourceName)->getAction($name);
    }
}