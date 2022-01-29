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

use Nours\RestAdminBundle\AdminManager;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\AcceptHeader;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * RequestListener who generates resource and action attributes based
 * on infos provided by the routing loader.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RequestListener implements EventSubscriberInterface
{
    private $adminManager;

    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $attributes = $request->attributes;

        // Resource and action parameters
        $resourceName = $attributes->get('_resource');
        $actionName   = $attributes->get('_action');

        if ($resourceName && $actionName) {
            if ($resource = $this->adminManager->getResource($resourceName)) {
                $action = $resource->getAction($actionName);
                if (!$action) {
                    throw new RuntimeException("Action ".$actionName." not found for resource ".$resourceName);
                }

                $attributes->set('resource', $resource);
                $attributes->set('action', $action);
            } else {
                throw new RuntimeException("Resource ".$resourceName." not found");
            }
        }

        // Guess request format on accept headers in order to initialize it's format
        $format = $request->get('_format');
        if (empty($format)) {
            $accept = AcceptHeader::fromString($request->headers->get('Accept'));
            if ($accept->has('application/json')) {
                $request->attributes->set('_format', 'json');
            } else {
                $request->attributes->set('_format', 'html');
            }
        }
    }


    public static function getSubscribedEvents()
    {
        // Must be executed after router listener (which has priority 32)
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 24)
        );
    }
}