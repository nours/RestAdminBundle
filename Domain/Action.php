<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Domain;
use Doctrine\Common\Inflector\Inflector;

/**
 * Resource action description.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Action
{
    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $resource;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param array $config
     */
    public function __construct(Resource $resource, array $config)
    {
        $this->resource = $resource;
        $this->config   = $config;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getConfig('name');
    }

    /**
     * Resource full name prepended to this action's name.
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->getResource()->getFullName() . ':' . $this->getName();
    }

    /**
     * @return bool
     */
    public function hasInstance()
    {
        return $this->getConfig('instance');
    }

    /**
     * @return bool
     */
    public function isBulk()
    {
        return $this->getConfig('bulk');
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getConfig('type');
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->getConfig('form', $this->getResource()->getForm());
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->getConfig('role');
    }

    /**
     * If this action is read only (it only exposes data and do not have forms and GET routes only).
     *
     * It is configured by read_only option
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        return $this->getConfig('read_only');
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->getConfig('template');
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->getConfig('controller');
    }

    /**
     * @return array
     */
    public function getHandlers()
    {
        return $this->getConfig('handlers', array());
    }

    /**
     * @param string $name
     * @return string
     */
    public function getRouteName($name = null)
    {
        return $this->resource->getRouteName($name ?: $this->getName());
    }

    /**
     * @param mixed $data
     * @return array
     */
    public function getRouteParams($data = null)
    {
        $resource = $this->resource;

        if ($this->isBulk()) {
            if (!is_array($data)) {
                throw new \InvalidArgumentException(sprintf(
                    "Bulk action %s needs an array of %s",
                    $this->getFullName(), $resource->getName()
                ));
            }
            return $resource->getCollectionRouteParams($data);
        } elseif ($this->hasInstance()) {
            // An instance is needed
            if (empty($data)) {
                throw new \InvalidArgumentException(sprintf(
                    "Missing %s instance to generate route params for action %s",
                    $resource->getName(), $this->getFullName()
                ));
            }

            return $resource->getResourceRouteParams($data);
        } elseif ($parent = $resource->getParent()) {
            if (empty($data)) {
                throw new \InvalidArgumentException(sprintf(
                    "Missing parent %s to generate route params for action %s",
                    $parent->getName(), $this->getFullName()
                ));
            }

            // $data can be an instance of either the resource itself or its parent
            if ($resource->isResourceInstance($data)) {
                return $resource->getRouteParamsFromData($data);
            } elseif ($parent->isResourceInstance($data)) {
                return $parent->getResourceRouteParams($data);
            }
        } else {
            return array();
        }
    }

    /**
     * Duplicate this action for another resource.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return Action
     */
    public function duplicate(Resource $resource)
    {
        $clone = clone $this;
        $clone->resource = $resource;
        return $clone;
    }

    /**
     * Get URI path to this action.
     *
     * Suffix defaults to action slug (leave null). To hide it, pass ''.
     *
     * @param null $suffix
     * @return string
     */
    public function getUriPath($suffix = null)
    {
        if (is_null($suffix)) {
            // Default suffix is action param name
            $suffix = str_replace('_', '-', Inflector::tableize($this->getName()));
        }
        return $this->getResource()->getUriPath($suffix, $this->hasInstance());
    }

    /**
     * Default route parameters for action prototypes
     *
     * @return array
     */
    public function getPrototypeRouteParams()
    {
        return $this->getResource()->getPrototypeRouteParams($this->hasInstance());
    }

    /**
     * @return array
     */
    public function getPrototypeParamsMapping()
    {
        return $this->getResource()->getPrototypeParamsMapping($this->hasInstance());
    }
}