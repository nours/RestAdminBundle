<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\DependencyInjection;

use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class RestAdminExtensionTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RestAdminExtensionTest extends AdminTestCase
{
    public function testIndexActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.index');

        $this->assertEquals(array(
            'template'   => 'index.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:index',
            'icon'       => 'list',        // See Tests/app/config/config_test.yml
            'default_option' => 'baz'
        ), $params);
    }


    public function testGetActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.get');

        $this->assertEquals(array(
            'template'   => 'get.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:get',
            'default_option' => 'foobar'        // See Tests/app/config/config_test.yml
        ), $params);
    }


    public function testCreateActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.create');

        $this->assertEquals(array(
            'template'   => 'create.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
//            'form'       => null,
            'icon'       => 'plus',        // See Tests/app/config/config_test.yml
            'default_option' => 'foobar'
        ), $params);
    }


    public function testEditActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.edit');

        $this->assertEquals(array(
            'template'   => 'edit.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
//            'form'       => null,
            'icon'       => 'pencil',        // See Tests/app/config/config_test.yml
            'default_option' => 'foobar'
        ), $params);
    }


    public function testDeleteActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.delete');

        $this->assertEquals(array(
            'template'   => 'delete.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
            'form'       => 'Nours\RestAdminBundle\Form\Type\DeleteType',
            'icon'       => 'trash',        // See Tests/app/config/config_test.yml
            'default_option' => 'foobar'
        ), $params);
    }


    public function testBulkDeleteActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.bulk_delete');

        $this->assertEquals(array(
            'template'   => 'bulk_delete.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
            'form'       => 'Nours\RestAdminBundle\Form\Type\BulkDeleteType',
            'default_option' => 'foobar'
        ), $params);
    }


    public function testServiceSerializer()
    {
        $serializer = $this->getContainer()->get('rest_admin.serializer');

        $this->assertSame($this->getContainer()->get('jms_serializer'), $serializer);
    }

    public function testServiceSerializationContext()
    {
        $context = $this->getContainer()->get('rest_admin.serialization_context');

        $this->assertNotNull($context);
    }
}