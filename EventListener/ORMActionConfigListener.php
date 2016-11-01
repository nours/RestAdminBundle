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

use Doctrine\ORM\EntityManagerInterface;
use Nours\RestAdminBundle\Event\ActionConfigEvent;
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
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param ActionConfigEvent $event
     */
    public function onActionConfig(ActionConfigEvent $event)
    {
        $priority = 20;

        // The listener should not apply on non Doctrine based resources
//        if (!$this->manager->getMetadataFactory()->isTransient($event->getResource()->getClass())) {
//            return;
//        }

        switch ($event->getActionName()) {
            case 'create' :
            case 'copy' :
                $event->addHandler('rest_admin.handler.orm:handleCreate', $priority);
                break;
            case 'edit' :
                $event->addHandler('rest_admin.handler.orm:handleUpdate', $priority);
                break;
                break;
            case 'delete' :
                $event->addHandler('rest_admin.handler.orm:handleDelete', $priority);
                break;
            case 'bulk_delete' :
                $event->addHandler('rest_admin.handler.orm:handleBulkDelete', $priority);
                break;
            default :
                // todo : refactor
                if ($event->getActionType() == 'form') {
                    $event->addHandler('rest_admin.handler.orm:handleUpdate', $priority);
                }
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            RestAdminEvents::ACTION => 'onActionConfig'
        );
    }
}