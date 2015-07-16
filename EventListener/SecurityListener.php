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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


/**
 * SecurityListener protects resource access (master requests only).
 *
 * To be dispatched right after RequestListener who populates resource and action.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class SecurityListener implements EventSubscriberInterface
{
    private $checker;

    /**
     * @param AuthorizationCheckerInterface $checker
     */
    public function __construct(AuthorizationCheckerInterface $checker)
    {
        $this->checker = $checker;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        // Search resource in attributes
        $resource = $event->getRequest()->attributes->get('resource');
        if (empty($resource)) {
            return;
        }

        // If the resource has a role, check it
        if ($role = $resource->getRole()) {
            if (!$this->checker->isGranted($role)) {
                throw new AccessDeniedHttpException(sprintf(
                    'Security check failure (%s is not granted for resource %s)',
                    $role, $resource->getFullName()
                ));
            }
        }
    }

    /**
     * Priority for this listener is 7, so it is executed just after the Firewall listener.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 7))
        );
    }
}