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
     * @return array
     */
    public function getRouteName()
    {
        return $this->resource->getRouteName($this->getName());
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
}