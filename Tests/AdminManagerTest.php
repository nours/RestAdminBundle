<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests;

use Nours\RestAdminBundle\AdminManager;

/**
 * Class AdminManagerTest
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminManagerTest extends AdminTestCase
{
    /**
     * @var AdminManager
     */
    private $manager;

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->getAdminManager();
    }

    /**
     * The resources from app/config.resources.yml should be loaded
     */
    public function testResourcesAreLoaded()
    {
        $resources = $this->manager->getResourceCollection();

        $this->assertTrue($resources->has('post'));
        $this->assertTrue($resources->has('post.comment'));

        $post    = $resources->get('post');
        $comment = $resources->get('post.comment');

        $this->assertSame($post, $comment->getParent());
    }

    /**
     * The resource collection in cache can be deserialized.
     */
    public function testResourceDeserialization()
    {
        $this->manager->getResourceCollection();

        $path = $this->getContainer()->getParameter('kernel.cache_dir') . '/RestResourceCollection.php';

        $this->assertFileExists($path);
        $collection = require $path;

        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\ResourceCollection', $collection);
        $this->assertTrue($collection->has('post'));
        $this->assertTrue($collection->has('post.comment'));
        $this->assertTrue($collection->has('post.comment_bis'));
    }


    public function testGetAction()
    {
        $action = $this->manager->getAction('post:index');

        $this->assertEquals('post:index', $action->getFullName());
        $this->assertEquals('index', $action->getName());
        $this->assertEquals('post', $action->getResource()->getName());

        $action = $this->manager->getAction('post.comment_bis:get');

        $this->assertEquals('post.comment_bis:get', $action->getFullName());
        $this->assertEquals('get', $action->getName());
        $this->assertEquals('post.comment_bis', $action->getResource()->getFullName());
    }
}