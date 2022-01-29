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

use Nours\RestAdminBundle\Controller\DefaultController;
use Nours\RestAdminBundle\Controller\FormController;
use Nours\RestAdminBundle\Controller\GetController;
use Nours\RestAdminBundle\Controller\IndexController;
use Nours\RestAdminBundle\Form\Type\BulkDeleteType;
use Nours\RestAdminBundle\Form\Type\DeleteType;
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
        $params = static::getContainer()->getParameter('rest_admin.actions.index');

        $this->assertEquals(array(
            'template'   => 'index.html.twig',
            'controller' => IndexController::class,
            'icon'       => 'list',        // See Tests/app/config/config_test.yml
            'default_option' => 'baz'
        ), $params);
    }


    public function testGetActionsParams()
    {
        $params = static::getContainer()->getParameter('rest_admin.actions.get');

        $this->assertEquals(array(
            'template'   => 'get.html.twig',
            'controller' => GetController::class,
            'default_option' => 'foobar'        // See Tests/app/config/config_test.yml
        ), $params);
    }


    public function testCreateActionsParams()
    {
        $params = static::getContainer()->getParameter('rest_admin.actions.create');

        $this->assertEquals(array(
            'template'   => 'create.html.twig',
            'controller' => FormController::class,
//            'form'       => null,
            'icon'       => 'plus',        // See Tests/app/config/config_test.yml
            'default_option' => 'foobar'
        ), $params);
    }


    public function testEditActionsParams()
    {
        $params = static::getContainer()->getParameter('rest_admin.actions.edit');

        $this->assertEquals(array(
            'template'   => 'edit.html.twig',
            'controller' => FormController::class,
//            'form'       => null,
            'icon'       => 'pencil',        // See Tests/app/config/config_test.yml
            'default_option' => 'foobar'
        ), $params);
    }


    public function testDeleteActionsParams()
    {
        $params = static::getContainer()->getParameter('rest_admin.actions.delete');

        $this->assertEquals(array(
            'template'   => 'delete.html.twig',
            'controller' => FormController::class,
            'form'       => DeleteType::class,
            'icon'       => 'trash',        // See Tests/app/config/config_test.yml
            'default_option' => 'foobar'
        ), $params);
    }


    public function testBulkDeleteActionsParams()
    {
        $params = static::getContainer()->getParameter('rest_admin.actions.bulk_delete');

        $this->assertEquals(array(
            'template'   => 'bulk_delete.html.twig',
            'controller' => FormController::class,
            'form'       => BulkDeleteType::class,
            'default_option' => 'foobar'
        ), $params);
    }


    public function testServiceSerializer()
    {
        $serializer = static::getContainer()->get('rest_admin.serializer');

        $this->assertSame(static::getContainer()->get('jms_serializer'), $serializer);
    }

    public function testServiceSerializationContext()
    {
        $context = static::getContainer()->get('rest_admin.serialization_context');

        $this->assertNotNull($context);
    }
}