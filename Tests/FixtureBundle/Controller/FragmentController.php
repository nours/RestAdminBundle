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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class FragmentController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FragmentController extends Controller
{
    /**
     * The template renders a post create action
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPostAction()
    {
        return $this->render('fragment/post_create.html.twig');
    }

    /**
     * The template renders a comment create action, using the post from main request attributes
     *
     * @param Post $parent
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createPostCommentAction(Post $parent)
    {
        return $this->render('fragment/post_comment_create.html.twig', array(
            'post' => $parent
        ));
    }

    /**
     * The template forwards to current resource's create action : the main request
     * attributes are used to discover everything.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        return $this->render('fragment/create.html.twig');
    }

    /**
     *
     * @param Comment $data
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPostCommentAction(Comment $data)
    {
        return $this->render('fragment/post_comment_edit.html.twig', array(
            'comment' => $data
        ));
    }

    /**
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction()
    {
        return $this->render('fragment/edit.html.twig');
    }
}