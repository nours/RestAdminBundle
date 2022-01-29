<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\EventListener;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\ParamFetcher\ParamFetcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Fetches parameters on master requests.
 *
 * Fetchers are labeled by name, and are lazy loaded from container (should need only one).
 *
 * Params from subrequest must be set manually.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ParamFetcherListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface|PsrContainerInterface
     */
    private $container;
    private $fetchers;
    private $defaultFetcher;

    /**
     * @param ContainerInterface|PsrContainerInterface $container
     * @param array $fetchers
     * @param $defaultFetcher
     */
    public function __construct($container, array $fetchers, $defaultFetcher)
    {
        $this->container = $container;
        $this->fetchers  = $fetchers;
        $this->defaultFetcher  = $defaultFetcher;
    }


    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->attributes->has('resource') && $request->attributes->has('action')) {
            if ($fetcher = $this->getParamFetcher($request)) {
                $fetcher->fetch($request);
            }
        }
    }

    /**
     * Finds the fetcher for request.
     *
     * @param Request $request
     * @return ParamFetcherInterface|null
     */
    private function getParamFetcher(Request $request): ?ParamFetcherInterface
    {
        /** @var DomainResource $resource */
        /** @var Action $action */
        $resource = $request->attributes->get('resource');
        $action   = $request->attributes->get('action');

        // Get fetcher name
        $name = $action->getConfig(
            'fetcher',
            $resource->getConfig('fetcher', $this->defaultFetcher)
        );

        // Putting false on fetcher deactivate the process
        if (empty($name)) {
            return null;
        }

        // Retrieve from map
        if (!isset($this->fetchers[$name])) {
            throw new \DomainException("Param fetcher $name is not registered, please declare a service with rest_admin.param_fetcher tag");
        }

        return $this->container->get($this->fetchers[$name]);
    }

    public static function getSubscribedEvents()
    {
        // Can be executed after all listeners
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 1)
        );
    }
}