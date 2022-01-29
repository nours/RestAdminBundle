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
use Doctrine\Persistence\ObjectManager;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\CommentResponse;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Composite;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\CompositeChild;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\PostExtension;

/**
 * Class LoadAll
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class LoadAll extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        // First post (id = 1), with one comment (id = 1), with one response (id = 1)
        // And with an author
        $post = new Post();
        $post->setContent('content');

        $comment = new Comment($post);
        $comment->setComment('comment');

        $response = new CommentResponse($comment);
        $response->setResponse('response');

        $extension= new PostExtension();
        $extension->setName('Extended');
        $post->setExtension($extension);
        $extension->setPost($post);

        $manager->persist($post);
        $manager->persist($comment);
        $manager->persist($response);
        $manager->persist($extension);

        // Other post (id = 2), without comment
        $post2 = new Post();
        $post2->setContent('second post');

        $manager->persist($post2);

        // Third post (id = 3), with two comment (id = 2, 3)
        $post3 = new Post();
        $post3->setContent('content 3');

        $comment2 = new Comment($post3);
        $comment2->setComment('comment 2');

        $comment3 = new Comment($post3);
        $comment3->setComment('comment 3');

        $manager->persist($post3);
        $manager->persist($comment2);
        $manager->persist($comment3);

        // Composite objects
        $composite1 = new Composite();
        $composite1->setId(1);
        $composite1->setName('first');
        $manager->persist($composite1);

        $composite2 = new Composite();
        $composite2->setId(1);
        $composite2->setName('second');
        $manager->persist($composite2);

        // Composite child
        $compositeChild = new CompositeChild();
        $compositeChild->setParent($composite1);
        $compositeChild->setId(1);
        $compositeChild->setName('child');
        $manager->persist($compositeChild);

        $compositeChild = new CompositeChild();
        $compositeChild->setParent($composite2);
        $compositeChild->setId(2);
        $compositeChild->setName('second child');
        $manager->persist($compositeChild);


        $manager->flush();
    }
}