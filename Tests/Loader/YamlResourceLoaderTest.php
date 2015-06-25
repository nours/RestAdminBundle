<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Loader;

use Nours\RestAdminBundle\Domain\ResourceCollection;
use Nours\RestAdminBundle\Event\ResourceCollectionEvent;
use Nours\RestAdminBundle\Event\RestAdminEvents;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\Config\Loader\DelegatingLoader;

class YamlResourceLoaderTest extends AdminTestCase
{
    /**
     * @var DelegatingLoader
     */
    private $loader;

    public function setUp()
    {
        parent::setUp();

        $this->loader = $this->get('rest_admin.loader');
    }

    /**
     * The post resource is configured in app/config/resources.yml
     *
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function testLoadPostResource()
    {
        /** @var ResourceCollection $resources */
        $resources = $this->loader->load('../config/resources.yml');

        $this->assertTrue($resources->has('post'));

        $post = $resources->get('post');

        // Actions
        $index = $post->getAction('index');
        $this->assertSame($index->getResource(), $post);

        $get = $post->getAction('get');
        $this->assertSame($get->getResource(), $post);

        $create = $post->getAction('create');
        $this->assertSame($create->getResource(), $post);

        $edit = $post->getAction('edit');
        $this->assertSame($edit->getResource(), $post);

        $delete = $post->getAction('delete');
        $this->assertSame($delete->getResource(), $post);

        $bulkDelete = $post->getAction('bulk_delete');
        $this->assertSame($bulkDelete->getResource(), $post);
    }


    /**
     * The post resource is configured in app/config/resources.yml
     *
     * @throws \Symfony\Component\Config\Exception\FileLoaderLoadException
     */
    public function testDispatchResourceConfigEvent()
    {
        $this->get('event_dispatcher')->addListener(RestAdminEvents::RESOURCE, function(ResourceCollectionEvent $event) {
            $resource = $event->getResource();

            $resource->setConfig('foo', 'bar');

            if ($resource->getName() == 'post') {
                $event->getCollection()->add($resource->duplicate('postbis'));
            }
        });

        /** @var ResourceCollection $resources */
        $resources = $this->loader->load('../config/resources.yml');

        $post = $resources->get('post.comment');
        $this->assertEquals('bar', $post->getConfig('foo'));

        $post = $resources->get('post.commentbis');
        $this->assertEquals('bar', $post->getConfig('foo'));

        $post = $resources->get('post');
        $this->assertEquals('bar', $post->getConfig('foo'));

        $this->assertTrue($resources->has('postbis'));
    }

}