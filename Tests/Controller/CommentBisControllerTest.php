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
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/posts/1/comment-bis');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>post.comment_bis index</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Index action.
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/posts/1/comment-bis/1');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertStringContainsString(
            '<h1>post.comment_bis 1</h1>',
            $response->getContent()
        );
        $this->assertStringContainsString(
            '<p>comment</p>',
            $response->getContent()
        );
    }

    /**
     * Create action form
     */
    public function testCreateFormAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/2/comment-bis/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>create post.comment_bis</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('comment_bis[submit]');
        $form = $button->form(array(
            'comment_bis[comment]' => 'creation'
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
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/1/comment-bis/1/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>edit post.comment_bis 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('comment_bis[submit]');
        $form = $button->form(array(
            'comment_bis[comment]' => 'updated!'
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