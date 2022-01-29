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
 * Class CompositeControllerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CompositeControllerTest extends AdminTestCase
{
    /**
     * Index action.
     */
    public function testIndexAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $client->request('GET', '/composites');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>composite index</h1>',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '1 - first',
            $client->getResponse()->getContent()
        );
        $this->assertStringContainsString(
            '1 - second',
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

        $client->request('GET', '/composites/1/first');

        $response = $client->getResponse();

        $this->assertTrue($response->isSuccessful());
        $this->assertStringContainsString(
            '<h1>composite 1 - first</h1>',
            $response->getContent()
        );
    }

    /**
     * Create action form
     */
    public function testCreateAction()
    {
        $client = static::createClient();
        $this->loadFixtures();

        $crawler = $client->request('GET', '/composites/create');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>create composite</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('composite[submit]');
        $form = $button->form(array(
            'composite[id]' => '2',
            'composite[name]' => 'third'
        ));

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/composites');

        $this->getEntityManager()->clear();
        $newComposite = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 2,
            'name' => 'third'
        ));

        // Object has been created
        $this->assertNotNull($newComposite);
        $this->assertEquals(2, $newComposite->getId());
        $this->assertEquals('third', $newComposite->getName());

        return $newComposite;
    }

    /**
     * Edit action form
     *
     * @depends testCreateAction
     * @param Composite $composite
     */
    public function testEditAction(Composite $composite)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/composites/' . $composite->getId() . '/' . $composite->getName() . '/edit');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>edit composite 2 - third</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('composite[submit]');
        $form = $button->form(array(
            'composite[id]' => '1',
            'composite[name]' => 'fourth'
        ));

//        echo($client->getResponse()->getContent());die;

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/composites');

        $this->getEntityManager()->clear();
        $updatedComposite = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'fourth'
        ));

        // Object has been created
        $this->assertNotNull($updatedComposite);
        $this->assertEquals(1, $updatedComposite->getId());
        $this->assertEquals('fourth', $updatedComposite->getName());

        return $updatedComposite;
    }

    /**
     * @depends testEditAction
     * @param Composite $composite
     */
    public function testDeleteAction(Composite $composite)
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/composites/' . $composite->getId() . '/' . $composite->getName() . '/delete');

        $this->assertTrue($client->getResponse()->isSuccessful());
        $this->assertStringContainsString(
            '<h1>delete composite 1 - fourth</h1>',
            $client->getResponse()->getContent()
        );

        $button = $crawler->selectButton('composite[submit]');
        $form = $button->form();

        $client->submit($form);

        $response = $client->getResponse();
        $this->assertRedirect($response, '/composites');

        // Object has been deleted
        $this->getEntityManager()->clear();
        $composite = $this->getEntityManager()->getRepository('FixtureBundle:Composite')->findOneBy(array(
            'id' => 1,
            'name' => 'fourth'
        ));

        $this->assertNull($composite);
    }
}