<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Table;

use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Nours\RestAdminBundle\Tests\FixtureBundle\Table\Type\CommentType;
use Nours\RestAdminBundle\Tests\FixtureBundle\Table\Type\PostType;
use Nours\TableBundle\Table\TableInterface;

/**
 * Class AdminExtensionTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminExtensionTest extends AdminTestCase
{
    public function testAdminResourceTable()
    {
        /** @var TableInterface $table */
        $table = $this->get('nours_table.factory')->createTable(PostType::class, array(
            'resource' => 'post'
        ));

        $this->assertEquals('/posts', $table->getOption('url'));
        $this->assertEquals('Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post', $table->getOption('class'));
    }

    public function testAdminParentResourceTable()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        /** @var TableInterface $table */
        $table = $this->get('nours_table.factory')->createTable(CommentType::class, array(
            'resource'   => 'post.comment',
            'route_data' => $post
        ));

        $this->assertEquals('/posts/1/comments', $table->getOption('url'));
        $this->assertEquals('Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment', $table->getOption('class'));
    }

    public function testTableUrlUsingCustomParams()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 1);

        /** @var TableInterface $table */
        $table = $this->get('nours_table.factory')->createTable(CommentType::class, array(
            'resource'   => 'post.comment',
            'route_data' => $post,
            'route_params' => array('foo' => 'bar')
        ));

        $this->assertEquals('/posts/1/comments?foo=bar', $table->getOption('url'));
    }

    /**
     * Table extension filters results according to parent resource.
     *
     * @see LoadAll fixture : post #3 with 2 comments (#2 and #3) are used
     *
     * This filter is enabled in test configuration.
     */
    public function testTableResourceParentFiltering()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->find('FixtureBundle:Post', 3);

        /** @var TableInterface $table */
        $table = $this->get('nours_table.factory')->createTable(CommentType::class, array(
            'resource'   => 'post.comment',
            'route_data' => $post
        ));

        $view = $table->handle()->createView();

        $data = $view->getData();

        $this->assertCount(2, $data);
        /** @var Comment $comment */
        $comment = $data[0];
        $this->assertEquals(2, $comment->getId());
        $comment = $data[1];
        $this->assertEquals(3, $comment->getId());
    }
}