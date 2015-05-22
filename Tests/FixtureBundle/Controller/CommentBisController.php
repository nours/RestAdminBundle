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

use Nours\RestAdminBundle\Annotation as Rest;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CommentBisController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment",
 *  name = "commentbis",
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
     * @param Request $request
     * @return Comment
     */
    public function makeComment(Request $request)
    {
        $post = $request->attributes->get('parent');

        return new Comment($post);
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
     * @Rest\Route("{commentbis}/test", methods={"GET"})
     * @Rest\Route("{commentbis}/test", name="other_do", methods={"POST"})
     */
    public function anotherTestAction()
    {

    }
}