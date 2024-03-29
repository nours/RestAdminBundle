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
 * Class PostControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class PostPrefixedControllerTest extends AdminTestCase
{
    /**
     * Index action.
     */
    public function testIndexAction()
    {
        $client = static::createClient();
        $client->catchExceptions(false);
        $this->loadFixtures();

        $client->request('GET', '/prefixed/posts');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>post_prefixed index</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Index action.
     */
    public function testIndexJsonAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/prefixed/posts.json');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertResponseContentType('application/json', $response);

        $data = json_decode($response->getContent());

        $this->assertCount(3, $data);       // 3 posts in fixtures
        $this->assertEquals(1, $data[0]->id);
        $this->assertEquals('content', $data[0]->content);
        $this->assertEquals(2, $data[1]->id);
        $this->assertEquals('second post', $data[1]->content);
    }

    /**
     * Index action.
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/prefixed/posts/1');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertStringContainsString(
            '<h1>post_prefixed 1</h1>',
            $response->getContent()
        );
        $this->assertStringContainsString(
            'content',
            $response->getContent()
        );
    }

    /**
     * Get action in JSON (using header .
     */
    public function testGetJsonAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request(
            'GET', '/prefixed/posts/1',
            array(),
            array(),
            array('HTTP_ACCEPT' => 'application/json')
        );

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertResponseContentType('application/json', $response);

        $data = json_decode($response->getContent());
        $this->assertEquals(1, $data->id);
        $this->assertEquals('content', $data->content);
    }

    /**
     * Create action form
     */
    public function testCreateFormAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/prefixed/posts/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>create post_prefixed</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form(array(
            'post[content]' => 'updated'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/prefixed/posts');

        $this->getEntityManager()->clear();
        $newPost = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(4);

        // Object has been created
        $this->assertNotNull($newPost);
        $this->assertEquals('updated', $newPost->getContent());
    }

    /**
     * Create action form
     */
    public function testEditFormAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/prefixed/posts/1/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>edit post_prefixed 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form(array(
            'post[content]' => 'edited'
        ));

//        echo($client->getResponse()->getContent());die;

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/prefixed/posts');

        $this->getEntityManager()->clear();
        $newPost = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);

        // Object has been created
        $this->assertEquals('edited', $newPost->getContent());
    }

    /**
     * Create action form
     */
    public function testDeleteFormAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/prefixed/posts/2/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>delete post_prefixed 2</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form();

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/prefixed/posts');

        // Object has been deleted
        $this->getEntityManager()->clear();
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(2);

        $this->assertTrue(null === $post);
    }
}