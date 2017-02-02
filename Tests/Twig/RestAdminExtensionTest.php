<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Twig;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Nours\RestAdminBundle\Twig\Extension\RestAdminExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * Class RestAdminExtensionTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RestAdminExtensionTest extends AdminTestCase
{
    /**
     * @var RestAdminExtension
     */
    private $extension;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function setUp()
    {
        parent::setUp();

        $this->twig = $this->get('twig');
        $this->requestStack = $this->get('request_stack');
//        $this->get('twig')->initRuntime();
        $this->extension    = $this->twig->getExtension(RestAdminExtension::class);
    }

    /**
     * The actionController method returns a controller reference using resource:action notation.
     */
    public function testColonNotationActionController()
    {
        $reference = $this->extension->createControllerReference('post:create');

        $this->assertActionControllerReference($reference, 'post', 'create');
    }

    /**
     * The action only notation assumes that the action must be retrieved from current resource (extracted from request)
     */
    public function testActionOnlyNotationActionController()
    {
        $request = new Request(array(), array(), array(
            'resource' => $this->getAdminManager()->getResource('post.comment')
        ));

        $this->requestStack->push($request);

        $reference = $this->extension->createControllerReference('create');

        $this->assertActionControllerReference($reference, 'post.comment', 'create');
    }

    /**
     * An action instance can be used to retrieve a controller reference.
     */
    public function testActionInstanceActionController()
    {
        $action = $this->getAdminManager()->getResource('post')->getAction('edit');

        $reference = $this->extension->createControllerReference($action);

        $this->assertActionControllerReference($reference, 'post', 'edit');
    }

    /**
     * The action only notation throws if no request is found from request stack
     */
    public function testActionOnlyNotationActionControllerThrowsIfNoRequest()
    {
        $this->assertNull($this->requestStack->getCurrentRequest());

        $this->setExpectedException("RuntimeException");
        $this->extension->createControllerReference('create');
    }

    /**
     * Action link rendering (without parameters)
     */
    public function testRenderActionLink()
    {
        $html = $this->extension->renderActionLink($this->twig, 'post:create');

        $this->assertNotFalse(strpos($html, '<a href="/posts/create">'));
    }

    /**
     * Action link rendering (with get parameters)
     */
    public function testRenderActionLinkWithParams()
    {
        $html = $this->extension->renderActionLink($this->twig, 'post:create', null, array(
            'routeParams' => array( 'foo' => 'bar' )
        ));

        $this->assertNotFalse(strpos($html, '<a href="/posts/create?foo=bar">'));
    }

    /**
     * Custom action template
     */
    public function testRenderCustomActionLink()
    {
        $html = $this->extension->renderActionLink($this->twig, 'foo:get');

        $this->assertEquals('action/custom.html.twig', $html);
    }

    /**
     * Action link rendering for one resource
     */
    public function testRenderResourceActionLink()
    {
        $this->loadFixtures();

        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);
        $html = $this->extension->renderActionLink($this->twig, 'post:edit', $post);

        $this->assertNotFalse(strpos($html, '<a href="/posts/1/edit">'));
    }

    /**
     * Action link rendering for one resource with custom params
     */
    public function testRenderResourceActionLinkWithParams()
    {
        $this->loadFixtures();

        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);
        $html = $this->extension->renderActionLink($this->twig, 'post:edit', $post, array(
            'routeParams' => array(
                'bar' => 'baz'
            )
        ));

        $this->assertNotFalse(strpos($html, '<a href="/posts/1/edit?bar=baz">'));
    }

    /**
     * Action link for sub resource
     */
    public function testRenderParentActionLink()
    {
        $this->loadFixtures();

        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);
        $html = $this->extension->renderActionLink($this->twig, 'post.comment:index', $post);

        $this->assertNotFalse(strpos($html, '<a href="/posts/1/comments">'));
    }

    /**
     * Link to sub resource instance
     */
    public function testRenderParentResourceActionLink()
    {
        $this->loadFixtures();

        $comment = $this->getEntityManager()->getRepository('FixtureBundle:Comment')->find(1);
        $html = $this->extension->renderActionLink($this->twig, 'post.comment:get', $comment);

        $this->assertNotFalse(strpos($html, '<a href="/posts/1/comments/1">'));
    }

    /**
     * Link prototype
     */
    public function testRenderActionPrototype()
    {
        $this->loadFixtures();

        $html = $this->extension->renderActionPrototype($this->twig, 'post.comment:get', array(
            'attr' => array('class' => 'btn')
        ));

        $this->assertNotFalse(strpos($html, '<a href="/posts/__post__/comments/__comment__" class="btn">'));
    }

    /**
     * Link prototype
     */
    public function testRenderActionPrototypeComposite()
    {
        $this->loadFixtures();

        $html = $this->extension->renderActionPrototype($this->twig, 'composite:get', array(
            'attr' => array('class' => 'btn')
        ));

        $this->assertNotFalse(strpos($html, '<a href="/composites/__composite_id__/__composite_name__" class="btn">'));
    }

    /**
     * Link prototype
     */
    public function testRenderActionPrototypeCompositeChild()
    {
        $this->loadFixtures();

        $html = $this->extension->renderActionPrototype($this->twig, 'composite.composite_child:get', array(
            'attr' => array('class' => 'btn')
        ));

        $url = '/composites/__composite_id__/__composite_name__/children/__composite_child_id__/__composite_child_name__';
        $this->assertNotFalse(strpos($html, '<a href="' . $url . '" class="btn">'));
    }

    /**
     * Relative URLS
     */
    public function testGetActionPath()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);

        $url = $this->extension->getActionPath('post:edit', $post);

        $this->assertEquals('/posts/1/edit', $url);
    }

    /**
     * Absolute URLS
     */
    public function testGetActionUrl()
    {
        $this->loadFixtures();
        $post = $this->getEntityManager()->getRepository('FixtureBundle:Post')->find(1);

        $url = $this->extension->getActionUrl('post:edit', $post);

        $this->assertEquals('http://tests.org/posts/1/edit', $url);
    }

    /**
     * Asserts the controller reference is well formed for a resource action.
     *
     * @param ControllerReference $reference
     * @param string $resourceName
     * @param string $actionName
     */
    private function assertActionControllerReference($reference, $resourceName, $actionName)
    {
        $this->assertInstanceOf('Symfony\Component\HttpKernel\Controller\ControllerReference', $reference);
        $this->assertArrayHasKey('resource', $reference->attributes);
        $this->assertArrayHasKey('action', $reference->attributes);

        /** @var DomainResource $resource */
        $resource = $reference->attributes['resource'];
        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\DomainResource', $resource);
        $this->assertEquals($resourceName, $resource->getFullName());

        /** @var Action $action */
        $action = $reference->attributes['action'];
        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\Action', $action);
        $this->assertEquals($actionName, $action->getName());

        $this->assertEquals($action->getController(), $reference->controller);
    }
}