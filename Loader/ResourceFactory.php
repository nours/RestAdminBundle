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
use Nours\RestAdminBundle\Event\ActionConfigEvent;
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
        $actions = $this->normalizeConfig($configs);

        foreach ($actions as $name => $config) {
            // Type may be defined in config, otherwise defaults to name
            $type = isset($config['type']) ? $config['type'] : $name;

            // Check if this action type is registered
            if (!$this->actionManager->hasActionBuilder($type)) {
                // Fallback to default action type
                $type = 'default';

                // Add the name to config
                $config['name'] = $name;
            }

            $builder = $this->actionManager->getActionBuilder($type);

            // Dispatch action config event
            $event = new ActionConfigEvent($resource, $name, $config);
            $this->dispatcher->dispatch(RestAdminEvents::ACTION_CONFIG, $event);

            $resource->addAction($builder->createAction($resource, $event->config));
        }
    }

    /**
     *
     *
     * @param array $configs
     * @return array
     */
    private function normalizeConfig(array $configs)
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
//            if (is_string($config)) {
//                $name   = $config;
//                $config = array();
//            }

//            if (empty($config['handlers'])) {
//                $config['handlers'] = array();
//            }

            $result[$name] = $config;
        }

        return $result;
    }
}