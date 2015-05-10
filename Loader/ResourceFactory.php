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
    private $builders;
    private $resourceClass;

    /**
     * @param ActionManager $builders
     * @param string $resourceClass
     */
    public function __construct(ActionManager $builders, $resourceClass)
    {
        $this->builders      = $builders;
        $this->resourceClass = $resourceClass;
    }

    /**
     * @param $class
     * @param array $configs
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function createResource($class, array $configs)
    {
        return new $this->resourceClass($class, $configs);
    }

    /**
     * Configure the actions for a resource from its config.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param array $configs
     */
    public function configureActions(Resource $resource, array $configs)
    {
        $actions = $this->prepareConfig($configs);

        // Append default actions
        foreach (array('index', 'get') as $name) {
            if (!isset($actions[$name])) {
                $actions[$name] = array();
            }
        }

        foreach ($actions as $name => $config) {
            $builder = $this->builders->getActionBuilder($name);

            $resource->addAction($builder->createAction($resource, $config));
        }
    }

    /**
     * @param array $configs
     * @return array
     */
    private function prepareConfig(array $configs)
    {
        $result = array();
        foreach ($configs as $name => $config) {
            if (is_string($config)) {
                $name   = $config;
                $config = array();
            }

            $result[$name] = $config;
        }

        return $result;
    }
}