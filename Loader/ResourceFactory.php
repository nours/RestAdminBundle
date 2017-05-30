<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Loader;

use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Nours\RestAdminBundle\Event\ActionConfigEvent;
use Nours\RestAdminBundle\Event\ActionConfigurationEvent;
use Nours\RestAdminBundle\Event\ResourceCollectionEvent;
use Nours\RestAdminBundle\Event\RestAdminEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


/**
 * Class ResourceFactory
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceFactory
{
    /**
     * @var ActionManager
     */
    private $actionManager;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    private $resourceClass;

    /**
     * @param ActionManager $actionManager
     * @param EventDispatcherInterface $dispatcher
     * @param string $resourceClass
     */
    public function __construct(ActionManager $actionManager, EventDispatcherInterface $dispatcher, $resourceClass)
    {
        $this->actionManager = $actionManager;
        $this->dispatcher    = $dispatcher;
        $this->resourceClass = $resourceClass;
    }

    /**
     * Factory for resource instances.
     *
     * @param $class
     * @param array $configs
     * @return DomainResource
     */
    public function createResource($class, array $configs)
    {
        return new $this->resourceClass($class, $configs);
    }

    /**
     * Configure the actions for a resource from a configuration array.
     *
     * @param DomainResource $resource
     * @param array $configs
     */
    public function configureActions(DomainResource $resource, array $configs)
    {
        $actions = $this->normalizeActionsConfig($resource, $configs);

        foreach ($actions as $name => $config) {
            // Type may be defined in config, otherwise defaults to name
            $type = isset($config['type']) ? $config['type'] : $name;

            // Check if this action type is registered
            if (!$this->actionManager->hasActionBuilder($type)) {
                // Fallback to custom action type
                $type = 'custom';
            }

            // Add the name to config
            $config['name'] = $name;

            $builder = $this->actionManager->getActionBuilder($type);

            // Dispatch deprecated action config event
            // Adding handlers using this event did overwrite default configuration.
            $event = new ActionConfigEvent($resource, $name, $type, $config);
            $this->dispatcher->dispatch(RestAdminEvents::ACTION, $event);

            // Create the action
            $action = $builder->createAction($resource, $event->config);
            $resource->addAction($action);

            // Add handlers from deprecated event
            if ($event->handlers) {
                trigger_error(sprintf(
                    "Adding handlers to action %s using RestAdminEvents::ACTION is deprecated. " .
                    "Please use RestAdminEvents::ACTION_CONFIG instead.",
                    $action->getFullName()
                ));

                $handlers = $action->getConfig('handlers');
                foreach ($event->handlers as $handler) {
                    $handlers[] = $handler;
                }
                $action->setConfig('handlers', $handlers);
            }

            // Dispatch action configuration after the action has been created.
            $event = new ActionConfigurationEvent($action);
            $this->dispatcher->dispatch(RestAdminEvents::ACTION_CONFIG, $event);
        }
    }

    /**
     * To called when a resource loading is done, to dispatch an event
     *
     * @param DomainResource $resource
     * @param ResourceCollection $collection
     */
    public function finishResource(DomainResource $resource, ResourceCollection $collection)
    {
        // Dispatch resource config event
        $event = new ResourceCollectionEvent($resource, $collection);
        $this->dispatcher->dispatch(RestAdminEvents::RESOURCE, $event);
    }

    /**
     * Normalizes actions configuration array.
     *
     * Automatically appends index and get actions by default if they are not set.
     *
     * @param DomainResource $resource
     * @param array $configs
     *
     * @return array
     */
    private function normalizeActionsConfig(DomainResource $resource, array $configs)
    {
        // Append index action if not set and resource is not single
        if (!array_key_exists('index', $configs) && !$resource->isSingleResource()) {
            $configs['index'] = array();
        }

        // Append get action
        if (!array_key_exists('get', $configs)) {
            $configs['get'] = array();
        }

        // Filter disabled actions
        $result = array();
        foreach ($configs as $name => $config) {
            // Disable the action if config is false
            if (false === $config) {
                continue;
            }

            $result[$name] = $config;
        }

        return $result;
    }
}