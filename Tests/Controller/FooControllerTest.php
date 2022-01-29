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
 * Checks custom paramfetcher to handles entity loading.
 *
 * @see FooController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FooControllerTest extends AdminTestCase
{

    /**
     * Index action.
     */
    public function testFooIndexAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/foos');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            'foo/index.html.twig',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '1-2-3-4-5-',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Get action.
     */
    public function testFooGetAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/foos/42');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            'foo/get.html.twig',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            'foo 42',
            $client->getResponse()->getContent()
        );
    }
}