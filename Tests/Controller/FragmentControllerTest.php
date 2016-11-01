<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Twig;

use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * Class FragmentControllerTest.
 *
 * The fragment controller is used as proxy to render fragments from Twig templates.
 *
 * The test subject is the Twig RestAdminExtension (@see \Nours\RestAdminBundle\Twig\Extension\RestAdminExtension).
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FragmentControllerTest extends AdminTestCase
{
    /**
     * @var HttpKernel
     */
    private $httpKernel;

    public function setUp()
    {
        parent::setUp();

        $this->httpKernel = $this->get('http_kernel');
    }

    /**
     * @see Tests/app/Resources/fragment/post_create.html.twig
     */
    public function testRenderPostCreateFragmentAction()
    {
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:createPost'
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>create post</h1>',
            $response->getContent()
        );
    }

    /**
     * @see Tests/app/Resources/fragment/post_comment_create.html.twig
     */
    public function testRenderPostCommentCreateFragmentAction()
    {
        $this->loadFixtures();
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:createPostComment',
            'parent' => $this->getEntityManager()->find('FixtureBundle:Post', 1)
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertSuccessful($response);
        $this->assertContains(
            '<h1>create post.comment</h1>',
            $response->getContent()
        );
    }

    /**
     * @see Tests/app/Resources/fragment/create.html.twig
     */
    public function testRenderCreateFragmentActionWithPostResource()
    {
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:create',
            'resource'    => $this->getAdminManager()->getResource('post')
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>create post</h1>',
            $response->getContent()
        );
    }

    /**
     * @see Tests/app/Resources/fragment/create.html.twig
     */
    public function testRenderCreateFragmentActionWithPostCommentResource()
    {
        $this->loadFixtures();
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:create',
            'resource'    => $this->getAdminManager()->getResource('post.comment'),
            'parent'      => $this->getEntityManager()->find('FixtureBundle:Post', 1)
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>create post.comment</h1>',
            $response->getContent()
        );
    }

    /**
     *
     * @see Tests/app/Resources/fragment/post_comment_edit.html.twig
     */
    public function testRenderPostCommentEditFragmentAction()
    {
        $this->loadFixtures();
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:editPostComment',
            'data'        => $this->getEntityManager()->find('FixtureBundle:Comment', 1)
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>edit post.comment 1</h1>',
            $response->getContent()
        );
    }

    /**
     *
     * @see Tests/app/Resources/fragment/post_comment_edit.html.twig
     */
    public function testRenderEditFragmentActionWithPostResource()
    {
        $this->loadFixtures();
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:edit',
            'resource'    => $this->getAdminManager()->getResource('post'),
            'data'        => $this->getEntityManager()->find('FixtureBundle:Post', 2)
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>edit post 2</h1>',
            $response->getContent()
        );
    }

    /**
     *
     * @see Tests/app/Resources/fragment/post_comment_edit.html.twig
     */
    public function testRenderEditFragmentActionWithPostCommentResource()
    {
        $this->loadFixtures();
        $request = new Request(array(), array(), array(
            '_controller' => 'FixtureBundle:Fragment:edit',
            'resource'    => $this->getAdminManager()->getResource('post.comment'),
            'data'        => $this->getEntityManager()->find('FixtureBundle:Comment', 1)
        ));

        $response = $this->httpKernel->handle($request);

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>edit post.comment 1</h1>',
            $response->getContent()
        );
    }
}