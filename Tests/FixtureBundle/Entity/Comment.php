<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * A sample Comment class.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
#[ORM\Entity]
class Comment
{
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var Post
     */
    #[ORM\ManyToOne(targetEntity: Post::class, inversedBy: 'comments')]
    private $post;


    #[ORM\Column(type: 'string', nullable: true)]
    private $comment;

    /**
     * @var Collection|CommentResponse[]
     */
    #[ORM\OneToMany(mappedBy: 'comment', targetEntity: CommentResponse::class)]
    private $responses;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
        $post->getComments()->add($this);

        $this->responses = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Collection|CommentResponse[]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
}