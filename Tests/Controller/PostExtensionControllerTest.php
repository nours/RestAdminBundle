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
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;

/**
 * Class PostExtensionControllerTest
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class PostExtensionControllerTest extends AdminTestCase
{
    /**
     * Get action.
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/posts/1/extension');

        $this->assertSuccessful($client->getResponse());
        $this->assertStringContainsString(
            '<h1>post.extension 1</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Create action form
     */
    public function testCreateAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/2/extension/create');

        $this->assertSuccessful($client->getResponse());
        $this->assertStringContainsString(
            '<h1>create extension from post 2</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('extension[submit]');
        $form = $button->form(array(
            'extension[name]' => 'Foo Bar'
        ));

        $client->submit($form);

        $response = $client->getResponse();

        // Default redirect handler redirects to parent index
        $this->assertRedirect($response, '/posts');

        $this->getEntityManager()->clear();
        /** @var Post $post */
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(2);
        $extension = $post->getExtension();

        // Object has been created
        $this->assertNotNull($extension);
        $this->assertEquals('Foo Bar', $extension->getName());
    }

    /**
     * Edit action form
     */
    public function testEditAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/1/extension/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>edit post.extension 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('extension[submit]');
        $form = $button->form(array(
            'extension[name]' => 'edited'
        ));

//        echo($client->getResponse()->getContent());die;

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        $this->getEntityManager()->clear();
        $data = $this->getEntityManager()->getRepository('FixtureBundle:PostExtension')->find(1);

        // Object has been created
        $this->assertEquals('edited', $data->getName());
    }

    /**
     * Create action form
     */
    public function testDeleteAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/1/extension/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>delete post.extension 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('extension[submit]');
        $form = $button->form();

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        // Object has been deleted
        $this->getEntityManager()->clear();
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);
        $pxtension = $this->getEntityManager()->getRepository('FixtureBundle:PostExtension')->find(1);

        $this->assertNull($pxtension);
        $this->assertNull($post->getExtension());
    }
}