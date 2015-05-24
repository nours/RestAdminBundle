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

use Nours\RestAdminBundle\Loader\ResourceFactory;
use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class ResourceFactoryTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceFactoryTest extends AdminTestCase
{
    /**
     * @var ResourceFactory
     */
    private $factory;

    public function setUp()
    {
        parent::setUp();

        $this->factory = $this->get('rest_admin.loader.resource_factory');
    }

    /**
     * Check resource instance creation
     */
    public function testCreateResource()
    {
        $resource = $this->makeFooResource();

        $this->assertEquals('Foo', $resource->getClass());
        $this->assertEquals('foo', $resource->getName());
        $this->assertEquals('foo', $resource->getFullName());
        $this->assertEquals('foos', $resource->getSlug());
        $this->assertEquals('form_foo', $resource->getForm());
    }

    /**
     * Check resource instance creation
     */
    public function testConfigureActionsAddsIndexAndGetActions()
    {
        $resource = $this->makeFooResource();

        $this->factory->configureActions($resource, array());

        $this->assertTrue($resource->hasAction('index'));
        $this->assertTrue($resource->hasAction('get'));
    }

    /**
     * Check resource instance creation
     */
    public function testConfigureActionsWithCoreActions()
    {
        $resource = $this->makeFooResource();

        $configs = array(
            'get' => false,
            'create' => array()
        );

        $this->factory->configureActions($resource, $configs);

        $this->assertTrue($resource->hasAction('index'));
        $this->assertTrue($resource->hasAction('create'));

        // Get action has been disabled
        $this->assertFalse($resource->hasAction('get'));
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function makeFooResource()
    {
        return $this->factory->createResource('Foo', array(
            'form' => 'form_foo'
        ));
    }
}