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
class PostControllerTest extends AdminTestCase
{

//    /**
//     * Create action form in json
//     */
//    public function testCreateJsonAction()
//    {
//        $this->loadFixtures();
//        $client = $this->getClient();
//
//        $client->request('POST', '/posts.json', array(
//            'post[content]' => 'created'
//        ));
//
//        $response = $client->getResponse();
//        $this->assertJson($response->getContent());
//
//        echo $response->getContent();die;
//        $object = json_decode($response->getContent());
//        $this->assertNotNull($object);
//        $this->assertEquals(3, $object->id);
//        $this->assertEquals('created', $object->content);
//
//        $this->getEntityManager()->clear();
//        $newPost = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(3);
//
//        // Object has been created
//        $this->assertNotNull($newPost);
//        $this->assertEquals('created', $newPost->getContent());
//    }

    /**
     * Index action.
     */
    public function testIndexAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts');


        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>post index</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Index action.
     */
    public function testIndexJsonAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts.json');

        $response = $client->getResponse();
        $this->assertTrue($response->isSuccessful());
        $this->assertResponseContentType('application/json', $response);

        $data = json_decode($response->getContent());

        $this->assertCount(2, $data);       // 2 posts in fixtures
        $this->assertEquals(1, $data[0]->id);
        $this->assertEquals('content', $data[0]->content);
        $this->assertEquals(2, $data[1]->id);
        $this->assertEquals('second post', $data[1]->content);
    }

    /**
     * Get action.
     */
    public function testGetAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts/1');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>post 1</h1>',
            $response->getContent()
        );
        $this->assertContains(
            'content',
            $response->getContent()
        );
    }

    /**
     * Get action returns 404 if entity is not found.
     */
    public function testGetAction404()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts/9999');

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Get action in JSON (using HTTP_ACCEPT header).
     */
    public function testGetJsonAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request(
            'GET', '/posts/1',
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
    public function testCreateAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>create post</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form(array(
            'post[content]' => 'updated'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        $this->getEntityManager()->clear();
        $newPost = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(3);

        // Object has been created
        $this->assertNotNull($newPost);
        $this->assertEquals('updated', $newPost->getContent());
    }

    /**
     * Edit action form
     */
    public function testEditAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>edit post 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form(array(
            'post[content]' => 'edited'
        ));

//        echo($client->getResponse()->getContent());die;

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        $this->getEntityManager()->clear();
        $newPost = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);

        // Object has been created
        $this->assertEquals('edited', $newPost->getContent());
    }

    /**
     * Copy action form
     */
    public function testCopyAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/copy');

        $this->assertSuccessful($client->getResponse());
//        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>copy post 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form(array(
            'post[content]' => 'copied'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        $this->getEntityManager()->clear();
        $newPost = $this->getEntityManager()->getRepository('FixtureBundle:Post')->findOneBy(array(
            'content' => 'copied'
        ));

        // Object has been created
        $this->assertNotNull($newPost);
        $this->assertEquals('copied', $newPost->getContent());
        $this->assertNotNull($newPost->getId());
        $this->assertNotEquals(1, $newPost->getId());
    }

    /**
     * Form action
     */
    public function testCustomFormAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/custom-form');

//        echo $client->getResponse()->getContent();die;

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>edit post 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form(array(
            'post[content]' => 'edited'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        $this->getEntityManager()->clear();
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);

        // Object has been edited
        $this->assertEquals('edited', $post->getContent());
    }

    /**
     * Edit action returns 404 if entity is not found.
     */
    public function testEditAction404()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts/9999/edit');

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Create action form
     */
    public function testDeleteAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/2/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>delete post 2</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form();

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        // Object has been deleted
        $this->getEntityManager()->clear();
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(2);

        $this->assertNull($post);
    }

    /**
     * Delete action returns 404 if entity is not found.
     */
    public function testDeleteAction404()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts/9999/delete');

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }


    public function testBulkDeleteAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/delete?id[]=1&id[]=2');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>bulk delete post</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('post[submit]');
        $form = $button->form();

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts');

        // All posts has been deleted
        $this->getEntityManager()->clear();
        $all = $this->getEntityManager()->getRepository('FixtureBundle:Post')->findAll();

        $this->assertCount(0, $all);
    }
}