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
     * @var RequestStack
     */
    private $requestStack;

    public function setUp()
    {
        parent::setUp();

        $this->requestStack = $this->get('request_stack');
        $this->extension    = $this->get('rest_admin.twig.extension');
    }

    /**
     * The actionController method returns a controller reference using resource:action notation.
     */
    public function testColonNotationActionController()
    {
        $reference = $this->extension->actionController('post:create');

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

        $reference = $this->extension->actionController('create');

        $this->assertActionControllerReference($reference, 'post.comment', 'create');
    }

    /**
     * An action instance can be used to retrieve a controller reference.
     */
    public function testActionInstanceActionController()
    {
        $action = $this->getAdminManager()->getResource('post')->getAction('edit');

        $reference = $this->extension->actionController($action);

        $this->assertActionControllerReference($reference, 'post', 'edit');
    }

    /**
     * The action only notation throws if no request is found from request stack
     */
    public function testActionOnlyNotationActionControllerThrowsIfNoRequest()
    {
        $this->assertNull($this->requestStack->getCurrentRequest());

        $this->setExpectedException("RuntimeException");
        $this->extension->actionController('create');
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

        /** @var \Nours\RestAdminBundle\Domain\Resource $resource */
        $resource = $reference->attributes['resource'];
        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\Resource', $resource);
        $this->assertEquals($resourceName, $resource->getFullName());

        /** @var Action $action */
        $action = $reference->attributes['action'];
        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\Action', $action);
        $this->assertEquals($actionName, $action->getName());

        $this->assertEquals($action->getController(), $reference->controller);
    }
}