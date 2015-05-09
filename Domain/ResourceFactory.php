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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;


/**
 * Class ResourceFactory
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceFactory
{
    /**
     * @var ControllerResolverInterface
     */
    private $resolver;

    /**
     * @param ControllerResolverInterface $resolver
     */
    public function __construct(ControllerResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Creates a new resource.
     *
     * Uses the creation callback if configured.
     *
     * @param Request $request
     * @return mixed
     */
    public function createResource(Request $request)
    {
        /** @var \Nours\RestAdminBundle\Domain\Resource $resource */
        $resource = $request->attributes->get('resource');

        if ($factory = $resource->getFactory()) {
            // Clone request to use it's _controller attribute for resolver
            $subRequest = clone $request;
            $subRequest->attributes->set('_controller', $factory);

            // Find controller
            $controller = $this->resolver->getController($subRequest);

            if ($controller === false) {
                throw new \DomainException(sprintf(
                    "Factory method %s for resource %s could not be resolved",
                    $factory, $resource->getFullName()
                ));
            }

            $arguments = $this->resolver->getArguments($request, $controller);

            return call_user_func_array($controller, $arguments);
        }

        $class = $resource->getClass();
        return new $class();
    }

}