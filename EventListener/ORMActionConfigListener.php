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
 * Class DoctrineORMHandlerListener
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ORMActionConfigListener implements EventSubscriberInterface
{

    public function __construct()
    {

    }

    public function onActionConfig(ActionConfigEvent $event)
    {
        $priority = 20;

        // todo : check if resource entity is known in em

        switch ($event->getActionName()) {
            case 'create' :
                $event->addHandler('rest_admin.handler.orm:handleCreate', $priority);
                break;
            case 'edit' :
                $event->addHandler('rest_admin.handler.orm:handleUpdate', $priority);
                break;
            case 'delete' :
                $event->addHandler('rest_admin.handler.orm:handleDelete', $priority);
                break;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            RestAdminEvents::ACTION_CONFIG => 'onActionConfig'
        );
    }
}