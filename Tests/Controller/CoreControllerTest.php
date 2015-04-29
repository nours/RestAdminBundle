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

use Nours\RestAdminBundle\Api\ApiEventDispatcher;
use Nours\RestAdminBundle\Api\ApiEvents;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Nours\RestAdminBundle\Controller\CoreController;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\Fixtures\Entity\Post;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CoreControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CoreControllerTest extends AdminTestCase
{
    /**
     * @var CoreController
     */
    private $controller;

    public function setUp()
    {
//        $this->controller = $this->get('rest_admin.core_controller');
    }

    /**
     * Index action dispatches a LOAD event, which should load a collection of resources.
     */
    public function testIndexAction()
    {
        $client = $this->getClient();
        $dispatcher = $this->get('rest_admin.core_controller')->getDispatcher();

        $dispatcher->addEventListener(ApiEvents::EVENT_LOAD, function(ApiEvent $event) {
            $event->setModel(array(
                new Post(13),
                new Post(29),
            ));
        });

        $crawler = $client->request('GET', '/posts');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>post index</h1>',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            '<li>13</li>',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            '<li>29</li>',
            $client->getResponse()->getContent()
        );
    }


    /**
     * Index get dispatches a GET event, which should load a single resource.
     */
    public function testGetAction()
    {
        $client = $this->getClient();
        $dispatcher = $this->get('rest_admin.core_controller')->getDispatcher();

        $post = new Post(123);
        $post->content = '<p>test</p>';

        $dispatcher->addEventListener(ApiEvents::EVENT_GET, function(ApiEvent $event) use ($post) {
            $event->setModel($post);
        });

        $crawler = $client->request('GET', '/posts/123');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>post 123</h1>',
            $client->getResponse()->getContent()
        );
        $this->assertContains(
            '<p>test</p>',
            $client->getResponse()->getContent()
        );
    }


    /**
     * GET request in JSON should return valid JSON.
     */
    public function testJsonGetAction()
    {
        $client = $this->getClient();

        $post = new Post(123);
        $post->content = '<p>test</p>';

        $this->getDispatcher()->addEventListener(ApiEvents::EVENT_GET, function(ApiEvent $event) use ($post) {
            $event->setModel($post);
        });

        $crawler = $client->request('GET', '/posts/123.json');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $deserialized = json_decode($client->getResponse()->getContent());

        $this->assertEquals($post->getId(), $deserialized->id);
        $this->assertEquals($post->content, $deserialized->content);
    }

    /**
     * @return ApiEventDispatcher
     */
    protected function getDispatcher()
    {
        return $this->get('rest_admin.core_controller')->getDispatcher();
    }

    /**
     * Create action dispatches a CREATE event, which should initialize a new resource.
     */
    public function testCreateFormAction()
    {
        $client = $this->getClient();

        $post = new Post(21);

        $this->getDispatcher()->addEventListener(ApiEvents::EVENT_CREATE, function(ApiEvent $event) use ($post) {
            $event->setModel($post);
        });

        $crawler = $client->request('GET', '/posts/new');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>create post</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Create action makes POST request available.
     */
    public function testCreateSuccessAction()
    {
        $client = $this->getClient();

        $post = new Post(21);

        $this->getDispatcher()->addEventListener(ApiEvents::EVENT_CREATE, function(ApiEvent $event) use ($post) {
            $event->setModel($post);
        });

        $foundPost = null;
        $this->getDispatcher()->addEventListener(ApiEvents::EVENT_SUCCESS, function(ApiEvent $event) use (&$foundPost) {
            $foundPost = $event->getModel();
            $event->setResponse(new Response());
        });

        $crawler = $client->request('POST', '/posts', array(
            'content' => 'blabla'
        ));

//        var_dump($client->getResponse());

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertEquals('', $client->getResponse()->getContent());

        $this->assertSame($post, $foundPost);
    }

    /**
     * Create action makes POST request available.
     */
    public function testCreateErrorAction()
    {
        $client = $this->getClient();

        $post = new Post(21);

        $this->getDispatcher()->addEventListener(ApiEvents::EVENT_CREATE, function(ApiEvent $event) use ($post) {
            $event->setModel($post);
        });

        $foundPost = null;
        $this->getDispatcher()->addEventListener(ApiEvents::EVENT_ERROR, function(ApiEvent $event) use (&$foundPost) {
            $foundPost = $event->getModel();
        });

        $crawler = $client->request('POST', '/posts', array(
            'content' => ''
        ));

        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertSame($post, $foundPost);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function createForm()
    {
        $form = $this->getMockBuilder('Symfony\Component\Form\FormInterface')
            ->getMock();

        return $form;
    }
}