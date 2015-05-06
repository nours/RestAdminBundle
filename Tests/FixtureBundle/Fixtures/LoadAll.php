<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;

/**
 * Class LoadAll
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class LoadAll extends AbstractFixture
{


    public function load(ObjectManager $manager)
    {
        $post = new Post();
        $post->setContent('content');

        $manager->persist($post);

        $comment = new Comment($post);
        $comment->setComment('comment');

        $manager->persist($comment);

        $post = new Post();
        $post->setContent('second post');

        $manager->persist($post);

        $manager->flush();
    }
}