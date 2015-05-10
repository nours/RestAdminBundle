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

use Nours\RestAdminBundle\ParamFetcher\DoctrineParamFetcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Fetches Doctrine based resources from request parameters.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineListener implements EventSubscriberInterface
{
    /**
     * @var DoctrineParamFetcher
     */
    private $repository;

    public function __construct(DoctrineParamFetcher $repository)
    {
        $this->repository = $repository;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $request = $event->getRequest();

        if ($request->attributes->has('resource') && $request->attributes->has('action')) {
            $this->repository->fetchParams($request);
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 20))
        );
    }
}