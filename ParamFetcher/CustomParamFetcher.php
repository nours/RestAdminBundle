<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\ParamFetcher;

use DomainException;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * This param fetcher relies on custom specific implementation, labelled using ParamFetcher annotation.
 *
 * It uses a ControllerResolverInterface to call the method defined in the fetcher_callback config option.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CustomParamFetcher implements ParamFetcherInterface
{
    private ControllerResolverInterface $controllerResolver;
    private ArgumentResolverInterface $argumentResolver;

    /**
     * CustomParamFetcher constructor.
     *
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

    public function fetch(Request $request): void
    {
        /** @var DomainResource $resource */
        /** @var Action $action */
        $resource = $request->attributes->get('resource');
        $action   = $request->attributes->get('action');

        $callback = $action->getConfig('fetcher_callback', $resource->getConfig('fetcher_callback'));

        if (empty($callback)) {
            throw new DomainException(sprintf(
                "The param fetcher callback for action %s is not defined",
                $action->getFullName()
            ));
        }

        $this->doFetch($request, $callback, $action);
    }


    private function doFetch(Request $request, $callback, Action $action)
    {
        // Init a request for resolver
        $subRequest = $request->duplicate();

        $subRequest->attributes->set('_controller', $callback);

        $controller = $this->controllerResolver->getController($subRequest);

        if ($controller === false) {
            throw new DomainException(sprintf(
                "Param Fetcher controller %s for action %s could not be resolved",
                $callback, $action->getFullName()
            ));
        }

        $arguments  = $this->argumentResolver->getArguments($request, $controller);

        call_user_func_array($controller, $arguments);
    }
}