<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Controller\Comment;

use Nours\RestAdminBundle\Annotation as Rest;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CommentBisController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment",
 *  name = "comment_bis",
 *  parent = "post",
 *  foo = "bar"
 * )
 *
 * @Rest\Action(
 *  "create", form = "comment"
 * )
 * @Rest\Action(
 *  "edit", form = "comment"
 * )
 */
class CommentBisController
{
    /**
     * @Rest\Factory()
     *
     * @param Post $parent
     * @return Comment
     */
    public function makeComment(Post $parent)
    {
        return new Comment($parent);
    }

    /**
     * @Rest\Handler("create")
     *
     * @return Response
     */
    public function onCreateSuccess()
    {
        return new Response('created!', 201);
    }

    /**
     * @Rest\Handler("edit", priority = 10)
     *
     * @return Response
     */
    public function onEditSuccess()
    {
        return new Response('success!');
    }

    /**
     * @Rest\Action()
     * @Rest\Route("test", methods={"GET"})
     */
    public function testAction()
    {

    }

    /**
     * @Rest\Action("other")
     * @Rest\Route("{comment_bis}/test", methods={"GET"})
     * @Rest\Route("{comment_bis}/test", name="other_do", methods={"POST"})
     */
    public function anotherTestAction()
    {

    }
}