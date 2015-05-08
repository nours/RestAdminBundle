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


/**
 * Class CommentController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment",
 *  parent = "post",
 *  service = "tests.controller.comment",
 *  foo = "bar"
 * )
 *
 * @Rest\Action(
 *  "create", form = "comment"
 * )
 */
class CommentController
{
    /**
     * @Rest\Factory()
     *
     * @param Request $request
     * @return Comment
     */
    public function createComment(Request $request)
    {
        $post = $request->attributes->get('parent');

        return new Comment($post);
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