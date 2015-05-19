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
            'icon'       => 'list'        // See Tests/app/config/config_test.yml
        ), $params);
    }


    public function testGetActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.get');

        $this->assertEquals(array(
            'template'   => 'get.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:get'
        ), $params);
    }


    public function testCreateActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.create');

        $this->assertEquals(array(
            'template'   => 'create.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
            'form'       => null,
            'icon'       => 'plus'        // See Tests/app/config/config_test.yml
        ), $params);
    }


    public function testEditActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.edit');

        $this->assertEquals(array(
            'template'   => 'edit.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
            'form'       => null
        ), $params);
    }


    public function testDeleteActionsParams()
    {
        $params = $this->getContainer()->getParameter('rest_admin.actions.delete');

        $this->assertEquals(array(
            'template'   => 'delete.html.twig',
            'controller' => 'NoursRestAdminBundle:Default:form',
            'form'       => 'rest_admin_delete'
        ), $params);
    }

}