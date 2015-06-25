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
 * Class CommentControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CommentControllerTest extends AdminTestCase
{
    /**
     * Index action.
     */
    public function testIndexAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts/1/comments');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>post.comment index</h1>',
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

        $client->request('GET', '/posts/1/comments/1');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertContains(
            '<h1>post.comment 1</h1>',
            $response->getContent()
        );
        $this->assertContains(
            'comment',
            $response->getContent()
        );
    }

    /**
     * Index action.
     */
    public function testGetAction404ForBadParent()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $client->request('GET', '/posts/9999/comments/1');

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Create action form
     */
    public function testCreateFormAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/2/comments/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>create post.comment</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('comment[submit]');
        $form = $button->form(array(
            'comment[comment]' => 'updated'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts/2/comments');

        $this->getEntityManager()->clear();
        $newComment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->findOneBy(array(
            'post' => $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(2)
        ));

        // Object has been created
        $this->assertEquals('updated', $newComment->getComment());
    }

    /**
     * Create action form
     */
    public function testEditFormAction()
    {
        $this->loadFixtures();
        $client = $this->getClient();

        $crawler = $client->request('GET', '/posts/1/comments/1/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertContains(
            '<h1>edit post.comment 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('comment[submit]');
        $form = $button->form(array(
            'comment[comment]' => 'updated!'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts/1/comments');

        $this->getEntityManager()->clear();
        $newComment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->find(1);

        // Object has been created
        $this->assertEquals('updated!', $newComment->getComment());
    }
}