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

use Doctrine\Common\Persistence\ManagerRegistry;
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
    /**
     * @var ManagerRegistry
     */
    private $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param ActionConfigEvent $event
     */
    public function onActionConfig(ActionConfigEvent $event)
    {
        $priority = 20;

        // todo : test this using a non Doctrine based resource
        if (!$this->registry->getManagerForClass($event->getResource()->getClass())) {
            return;
        }

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
            case 'bulk_delete' :
                $event->addHandler('rest_admin.handler.orm:handleBulkDelete', $priority);
                break;
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            RestAdminEvents::ACTION => 'onActionConfig'
        );
    }
}