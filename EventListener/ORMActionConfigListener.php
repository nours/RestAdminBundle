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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DoctrineORMHandlerListener
 *
 * todo : Refactor the handler concept for clearer ORM integration.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ORMActionConfigListener implements EventSubscriberInterface
{
    /**
     * @param ActionConfigurationEvent $event
     */
    public function onActionConfig(ActionConfigurationEvent $event)
    {
        $priority = 20;

        $action = $event->getAction();
        $ormAction = $action->getConfig('handler_action');

        switch ($ormAction) {
            case 'create' :
                if ($action->isBulk()) {
                    $event->addHandler('rest_admin.handler.orm::handleBulkCreate', $priority);
                } else {
                    $event->addHandler('rest_admin.handler.orm::handleCreate', $priority);
                }
                break;
            case 'update' :
                $event->addHandler('rest_admin.handler.orm::handleUpdate', $priority);
                break;
            case 'delete' :
                if ($action->isBulk()) {
                    $event->addHandler('rest_admin.handler.orm::handleBulkDelete', $priority);
                } else {
                    $event->addHandler('rest_admin.handler.orm::handleDelete', $priority);
                }
                break;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            RestAdminEvents::ACTION_CONFIG => 'onActionConfig',
        );
    }
}