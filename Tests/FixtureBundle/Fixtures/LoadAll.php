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
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Composite;
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
        // First post (id = 1), with one comment (id = 1)
        $post = new Post();
        $post->setContent('content');

        $comment = new Comment($post);
        $comment->setComment('comment');

        $manager->persist($post);
        $manager->persist($comment);

        // Other post (id = 2), without comment
        $post = new Post();
        $post->setContent('second post');

        $manager->persist($post);

        // Composite objects
        $composite1 = new Composite();
        $composite1->setId(1);
        $composite1->setName('first');
        $manager->persist($composite1);

        $composite2 = new Composite();
        $composite2->setId(1);
        $composite2->setName('second');
        $manager->persist($composite2);

        $manager->flush();
    }
}