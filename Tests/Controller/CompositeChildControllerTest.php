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
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Composite;

/**
 * Class CompositeChildControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CompositeChildControllerTest extends AdminTestCase
{
    /**
     * Index action.
     */
    public function testIndexAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/composites/1/first/children');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>composite.composite_child index</h1>',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            '1 - first - 1 - child',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Get action.
     */
    public function testGetAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/composites/1/first/children/1/child');

        $response = $client->getResponse();

//        echo $response->getContent();die;
        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>composite.composite_child 1 - first - 1 - child</h1>',
            $response->getContent()
        );
    }
}