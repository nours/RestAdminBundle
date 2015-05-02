<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Fixtures\Controller;

use Nours\RestAdminBundle\Annotation as Rest;


/**
 * Class CommentController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  name = "comment",
 *  parent = "post",
 *  class = "Nours\RestAdminBundle\Tests\Fixtures\Entity\Comment"
 * )
 */
class CommentController
{

}