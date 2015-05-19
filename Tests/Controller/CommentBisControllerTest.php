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
 * Class CommentBisControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CommentBisControllerTest extends AdminTestCase
{
    /**
     * Index action.
     */
    public function testIndexAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/commentbis');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>post.commentbis index</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Index action.
     */
    public function testGetAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/commentbis/1');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>post.commentbis 1</h1>',
            $response->getContent()
        );
        $this->assertContains(
            'comment',
            $response->getContent()
        );
    }

    /**
     * Create action form
     */
    public function testCreateFormAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/2/commentbis/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>create post.commentbis</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('commentbis[submit]');
        $form = $button->form(array(
            'commentbis[comment]' => 'creation'
        ));

        $client->submit($form);

        $response = $client->getResponse();

        // Response should be overrided by CommentBisController::onEditSuccess
        $this->assertTrue($response->getStatusCode() == 201);
        $this->assertEquals('created!', $response->getContent());

        $this->getEntityManager()->clear();
        $newComment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->findOneBy(array(
            'post' => $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(2)
        ));

        // Object has been created
        $this->assertNotNull($newComment);
        $this->assertEquals('creation', $newComment->getComment());
    }

    /**
     * Create action form
     */
    public function testEditFormAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/commentbis/1/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>edit post.commentbis 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('commentbis[submit]');
        $form = $button->form(array(
            'commentbis[comment]' => 'updated!'
        ));

        $client->submit($form);

        $response = $client->getResponse();

        // Response should be overrided by CommentBisController::onEditSuccess
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('success!', $response->getContent());

        $this->getEntityManager()->clear();
        $newComment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->find(1);

        // Object has been created
        $this->assertEquals('updated!', $newComment->getComment());
    }
}