<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Controller;

use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class SecurityControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class SecurityControllerTest extends AdminTestCase
{
    public function testIndexIsForbiddenForAnonymous()
    {
        $client = static::createClient();

        $client->request('GET', '/secured');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testIndexIsForbiddenForUsery()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'passuser',
        ));

        $client->request('GET', '/secured');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testIndexIsAllowedForAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'passadmin',
        ));
        $client->catchExceptions(false);

        $client->request('GET', '/secured');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCreateIsForbiddenForAnonymous()
    {
        $client = static::createClient();

        $client->request('GET', '/secured/create');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateIsForbiddenForUsery()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW' => 'passuser',
        ));

        $client->request('GET', '/secured/create');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateIsNotAllowedForAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'passadmin',
        ));

        $client->request('GET', '/secured/create');

        $this->assertEquals(403, $client->getResponse()->getStatusCode());
    }

    public function testCreateIsAllowedForSuperAdmin()
    {
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'super',
            'PHP_AUTH_PW' => 'passsuper',
        ));

        $client->request('GET', '/secured/create');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}