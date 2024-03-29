<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Controller;

use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FragmentController.
 *
 * The controller methods render templates which render rest actions as fragments.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FragmentController extends AbstractController
{
    /**
     * The template renders a post create action
     *
     * @return Response
     */
    public function createPost(): Response
    {
        return $this->render('fragment/post_create.html.twig');
    }

    /**
     * The template renders a comment create action, using the post from main request attributes
     *
     * @param Post $parent
     * @return Response
     */
    public function createPostComment(Post $parent): Response
    {
        return $this->render('fragment/post_comment_create.html.twig', array(
            'post' => $parent
        ));
    }

    /**
     * The template forwards to current resource's create action : the main request
     * attributes are used to discover everything.
     *
     * @return Response
     */
    public function create(): Response
    {
        return $this->render('fragment/create.html.twig');
    }

    /**
     *
     * @param Comment $data
     * @return Response
     */
    public function editPostComment(Comment $data): Response
    {
        return $this->render('fragment/post_comment_edit.html.twig', array(
            'comment' => $data
        ));
    }

    /**
     *
     * @return Response
     */
    public function edit(): Response
    {
        return $this->render('fragment/edit.html.twig');
    }
}