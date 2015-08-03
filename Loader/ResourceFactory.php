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
use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Nours\RestAdminBundle\Event\ActionConfigEvent;
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
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function createResource($class, array $configs)
    {
        return new $this->resourceClass($class, $configs);
    }

    /**
     * Configure the actions for a resource from a configuration array.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param array $configs
     */
    public function configureActions(Resource $resource, array $configs)
    {
        $actions = $this->normalizeActionsConfig($configs);

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

            // Dispatch action config event
            $event = new ActionConfigEvent($resource, $name, $type, $config);
            $this->dispatcher->dispatch(RestAdminEvents::ACTION, $event);

            $resource->addAction($builder->createAction($resource, $event->config));
        }
    }

    /**
     * To called when a resource loading is done, to dispatch an event
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param ResourceCollection $collection
     */
    public function finishResource(Resource $resource, ResourceCollection $collection)
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
     * @param array $configs
     * @return array
     */
    private function normalizeActionsConfig(array $configs)
    {
        // Append default actions if not set
        foreach (array('index', 'get') as $name) {
            if (!array_key_exists($name, $configs)) {
                $configs[$name] = array();
            }
        }

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