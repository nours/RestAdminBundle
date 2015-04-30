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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class RequestListener
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

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $attributes = $request->attributes;
        $resourceName = $attributes->get('_resource');
        $actionName   = $attributes->get('_action');

        if ($resourceName) {
            if ($resource = $this->adminManager->getResource($resourceName)) {
                $action = $resource->getAction($actionName);
                if (!$action) {
                    throw new \RuntimeException("Action ".$actionName." not found for resource ".$resourceName);
                }

                $attributes->set('_resource', $resource);
                $attributes->set('_action', $action);
            } else {
                throw new \RuntimeException("Resource ".$resourceName." not found");
            }
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 24))
        );
    }
}