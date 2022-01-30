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

use Nours\RestAdminBundle\View\ViewHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * RequestListener who generates resource and action attributes based
 * on infos provided by the routing loader.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ViewListener implements EventSubscriberInterface
{
    private $handler;

    /**
     * @param ViewHandler $handler
     */
    public function __construct(ViewHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param ViewEvent $event
     *
     * @return void
     */
    public function onKernelView(ViewEvent $event)
    {
        $request = $event->getRequest();

        if ($response = $this->handler->handle($event->getControllerResult(), $request)) {
            $event->setResponse($response);
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::VIEW => array(array('onKernelView', 32))
        );
    }
}