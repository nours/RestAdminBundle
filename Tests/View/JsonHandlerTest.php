<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\View;

use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;
use Nours\RestAdminBundle\Tests\FixtureBundle\Form\PostType;
use Nours\RestAdminBundle\View\JsonHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonHandlerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class JsonHandlerTest extends AdminTestCase
{
    /**
     * @var JsonHandler
     */
    private $jsonHandler;

    public function setUp()
    {
        $this->jsonHandler = new JsonHandler($this->get('rest_admin.serializer'), $this->get('rest_admin.serialization_context'));
    }

    public function testSupportsJson()
    {
        $request = new Request();
        $request->setRequestFormat('json');

        $this->assertTrue($this->jsonHandler->supports($request));
    }

    public function testDoNotSupportsHTML()
    {
        $request = new Request();

        $this->assertFalse($this->jsonHandler->supports($request));
    }

    public function testHandleSimpleData()
    {
        $request = new Request();
        $request->setRequestFormat('json');

        $response = $this->jsonHandler->handle('foobar', $request);

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals('"foobar"', $response->getContent());
    }

    public function testHandleForm()
    {
        $this->loadFixtures();

        $post = $this->getEntityManager()->find(Post::class, 2);

        /** @var FormInterface $form */
        $form = $this->get('form.factory')->create(PostType::class, $post);

        $request = new Request();
        $request->setRequestFormat('json');

        $response = $this->jsonHandler->handle($form, $request);

        $this->assertEquals('application/json', $response->headers->get('Content-Type'));

        $data = $this->get('jms_serializer')->deserialize($response->getContent(), Post::class, 'json');

        $this->assertTrue($data instanceof Post);
        $this->assertEquals($post->getId(), $data->getId());
        $this->assertEquals($post->getContent(), $data->getContent());
    }
}