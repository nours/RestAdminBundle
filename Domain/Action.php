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
     * @var array
     */
    private $config = array();

    /**
     * @param $name
     * @param array $config
     */
    public function __construct($name, array $config)
    {
        $this->name    = $name;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return string
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
        return $this->config['form'];
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
}