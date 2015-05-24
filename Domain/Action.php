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
     * The action name
     *
     * @var string
     */
    private $name;

    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $resource;

    /**
     * @var array
     */
    private $config = array();

    /**
     * @param $name
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param array $config
     */
    public function __construct($name, Resource $resource, array $config)
    {
        $this->name     = $name;
        $this->resource = $resource;
        $this->config   = $config;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
        return $this->getConfig('type', $this->name);
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
     * @return string
     */
    public function getForm()
    {
        return $this->getConfig('form');
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->getConfig('role');
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
}