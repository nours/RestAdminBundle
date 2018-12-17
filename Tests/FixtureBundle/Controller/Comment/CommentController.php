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
use Nours\RestAdminBundle\Tests\FixtureBundle\Form\CommentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class CommentController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment",
 *  parent = "post",
 *  parent_property_path = "post",
 *  service = "tests.controller.comment",
 *  foo = "bar"
 * )
 *
 * @Rest\Action(
 *  "create", form = CommentType::class
 * )
 * @Rest\Action(
 *  "edit", form = CommentType::class
 * )
 * @Rest\Action(
 *  "copy", form = CommentType::class
 * )
 * @Rest\Action("bulk_delete")
 */
class CommentController
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
     * @Rest\Factory("copy")
     *
     * @param Comment $data
     * @return Comment
     */
    public function copyComment(Comment $data)
    {
        $clone = clone $data;

        return $clone;
    }

    /**
     * @return Response
     */
    public function onEditSuccess()
    {
        return new Response('success!');
    }

    /**
     * @Rest\Action(
     *  "publish"
     * )
     */
    public function publishAction()
    {

    }
}