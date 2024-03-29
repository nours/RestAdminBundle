<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Domain;

use DomainException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;


/**
 * Creates resource instances.
 *
 * A resource can define its own factory callback, using SF2 controller syntax. Otherwise, the new operator is called
 * on resource class.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceDataFactory
{
    private ControllerResolverInterface $controllerResolver;
    private ArgumentResolverInterface $argumentResolver;

    /**
     * @param ControllerResolverInterface $controllerResolver
     * @param ArgumentResolverInterface $argumentResolver
     */
    public function __construct(
        ControllerResolverInterface $controllerResolver,
        ArgumentResolverInterface $argumentResolver
    ) {
        $this->controllerResolver = $controllerResolver;
        $this->argumentResolver   = $argumentResolver;
    }


    public function handle(Request $request)
    {
        /** @var Action $action */
        $action = $request->attributes->get('action');

        // Use action factory if defined (mandatory if present)
        if ($factory = $action->getFactory()) {
            return $this->makeData($request, $action, $factory);
        }

        // Use current data
        if ($data = $request->attributes->get('data')) {
            return $data;
        }

        // Use resource factory
        if ($factory = $action->getResource()->getFactory()) {
            return $this->makeData($request, $action, $factory);
        }

        // Use default constructor
        $class = $action->getResource()->getClass();
        return new $class();
    }

    /**
     * Creates a new resource.
     *
     * Uses the creation callback if configured.
     *
     * @param Request $request
     * @param Action $action
     * @param ?string $factory
     * @return mixed
     */
    private function makeData(Request $request, Action $action, ?string $factory)
    {
        // Clone request to use its _controller attribute for resolver
        $subRequest = $request->duplicate();
        $subRequest->attributes->set('_controller', $factory);

        // Find controller
        $controller = $this->controllerResolver->getController($subRequest);

        if ($controller === false) {
            throw new DomainException(sprintf(
                "Factory method %s for action %s could not be resolved",
                $factory, $action->getFullName()
            ));
        }

        $arguments = $this->argumentResolver->getArguments($request, $controller);

        return call_user_func_array($controller, $arguments);
    }

}