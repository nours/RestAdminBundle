<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Loader;

use Nours\RestAdminBundle\Loader\AnnotationClassLoader;
use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class AnnotationClassLoaderTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AnnotationClassLoaderTest extends AdminTestCase
{

    public function testLoadClass()
    {
        $class = 'Nours\RestAdminBundle\Tests\FixtureBundle\Controller\CommentController';

        $loader = new AnnotationClassLoader($this->get('annotation_reader'), $this->get('rest_admin.loader.resource_factory'));

        $resources = $loader->load($class);

        $this->assertCount(1, $resources);
        $this->assertTrue($resources->has('post.comment'));

        $resource = $resources->get('post.comment');

        $this->assertEquals('post', $resource->getParentName());

        $this->assertCount(4, $resource->getActions());

        $publish = $resource->getAction('publish');

        $this->assertNotNull($publish);
        $this->assertEquals('tests.controller.comment:publishAction', $publish->getController());
    }

}