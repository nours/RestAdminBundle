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
        $table = $this->get('nours_table.factory')->createTable('post', array(
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
        $table = $this->get('nours_table.factory')->createTable('comment', array(
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
        $table = $this->get('nours_table.factory')->createTable('comment', array(
            'resource'   => 'post.comment',
            'route_data' => $post,
            'route_params' => array('foo' => 'bar')
        ));

        $this->assertEquals('/posts/1/comments?foo=bar', $table->getOption('url'));
    }
}