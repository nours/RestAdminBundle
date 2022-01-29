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
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;


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
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/posts/1/comments');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>post.comment index</h1>',
            $client->getResponse()->getContent()
        );
    }

    /**
     * Get action.
     */
    public function testGetAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/posts/1/comments/1');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertStringContainsString(
            '<h1>post.comment 1</h1>',
            $response->getContent()
        );
        $this->assertStringContainsString(
            'comment',
            $response->getContent()
        );
    }

    /**
     * Get action.
     */
    public function testGetAction404ForBadParent()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/posts/9999/comments/1');

        $response = $client->getResponse();

        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Create action form
     */
    public function testCreateFormAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/2/comments/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
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
     * Edit action form
     */
    public function testEditFormAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/1/comments/1/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
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

    /**
     * Copy action
     */
    public function testCopyAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/posts/1/comments/1/copy');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>copy post.comment 1</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('comment[submit]');
        $form = $button->form(array(
            'comment[comment]' => 'copied!'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/posts/1/comments');

        $this->getEntityManager()->clear();
        /** @var Comment $newComment */
        $newComment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->findOneBy(array(
            'comment' => 'copied!'
        ));

        // Object has been created
        $this->assertNotNull($newComment);
        $this->assertEquals(1, $newComment->getPost()->getId());
        $this->assertEquals('copied!', $newComment->getComment());
    }
}