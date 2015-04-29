<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Fixtures\Entity;

/**
 * A sample Comment class.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Comment
{
    private $id;

    /**
     * @var Post
     */
    public $post;

    public $comment;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}