<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Api;

use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Nours\RestAdminBundle\Api\Event\EventSubscriber;
use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * The ApiEventDispatcher provides a way to decouple action implementation from common use cases implemented in kernels.
 *
 * The default events are loaded on the fly for the resource and action needed on dispatch. The default listener are provided
 * by extensions, notified on the first resource + action dispatch.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ApiEventDispatcher
{
    private $events = array();

    /**
     * @var AdminManager
     */
    private $manager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @var ActionManager
     */
    private $actionManager;


    public function __construct(AdminManager $manager, ActionManager $actionManager)
    {
        $this->manager = $manager;
        $this->actionManager = $actionManager;
        $this->dispatcher = new EventDispatcher();
    }

    /**
     * Registers a new event subscriber
     *
     * @param EventSubscriber $subscriber
     */
    public function addEventSubscriber(EventSubscriber $subscriber)
    {
        $events = $subscriber->getSubscribedEvents();

        foreach ($events as $eventName => $params) {
            if (is_string($params)) {
                $this->dispatcher->addListener($eventName, array($subscriber, $params), 0);
            } elseif (is_string($params[0])) {
                $this->dispatcher->addListener($eventName, array($subscriber, $params[0]), isset($params[1]) ? $params[1] : 0);
            } else {
                foreach ($params as $listener) {
                    $this->dispatcher->addListener($eventName, array($subscriber, $listener[0]), isset($listener[1]) ? $listener[1] : 0);
                }
            }
        }
    }

    /**
     * Dispatches an api event.
     *
     * The event is distributed to resource specific listener first, before global subscribers :
     *  - post.index.load
     *  - post.load
     *  - load
     *
     * @param string $eventName
     * @param ApiEvent $event
     * @return ApiEvent
     */
    public function dispatch($eventName, ApiEvent $event)
    {
        $resource = $event->getResource();

        $events = array(
            $resource->getFullName() . '.' . $eventName, $eventName
        );

        foreach ($events as $name) {
            $this->dispatcher->dispatch($name, $event);

            if ($event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    /**
     * Adds an event listener for any resource action
     *
     * @param $eventName
     * @param $listener
     * @param int $priority
     */
    public function addEventListener($eventName, $listener, $priority = 0)
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);
    }
}