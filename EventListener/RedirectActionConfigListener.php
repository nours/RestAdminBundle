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

use Nours\RestAdminBundle\Event\ActionConfigEvent;
use Nours\RestAdminBundle\Event\RestAdminEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class RedirectActionConfigListener.
 *
 * Configure a default action handler which creates a redirect response to the resource index.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RedirectActionConfigListener implements EventSubscriberInterface
{
    public function onActionConfig(ActionConfigEvent $event)
    {
        // Skip readonly actions
        // todo : find a way to use read_only here
        if (in_array($event->getActionName(), array('index', 'get'))) {
            return;
        }

        $event->addHandler('rest_admin.handler.redirect:handleRedirect', -20);
    }

    public static function getSubscribedEvents()
    {
        return array(
            RestAdminEvents::ACTION => 'onActionConfig'
        );
    }
}