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
        $this->assertNotNull($index);
        $this->assertSame($index->getResource(), $post);

        $get = $post->getAction('get');
        $this->assertNotNull($get);
        $this->assertSame($get->getResource(), $post);

        $create = $post->getAction('create');
        $this->assertNotNull($create);
        $this->assertSame($create->getResource(), $post);

        $edit = $post->getAction('edit');
        $this->assertNotNull($edit);
        $this->assertSame($edit->getResource(), $post);

        $delete = $post->getAction('delete');
        $this->assertNotNull($delete);
        $this->assertSame($delete->getResource(), $post);
    }

}