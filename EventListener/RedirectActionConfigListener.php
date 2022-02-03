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

use Nours\RestAdminBundle\Event\ActionConfigurationEvent;
use Nours\RestAdminBundle\Event\RestAdminEvents;
use Nours\RestAdminBundle\Handler\RedirectHandler;
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
    public function onActionConfig(ActionConfigurationEvent $event)
    {
        // Skip readonly actions
        if ($event->getAction()->isReadOnly()) {
            return;
        }

        // Add default redirect handler
        $event->addHandler(RedirectHandler::class . '::handleRedirect', -20);
    }

    public static function getSubscribedEvents()
    {
        return array(
            RestAdminEvents::ACTION_CONFIG => 'onActionConfig'
        );
    }
}