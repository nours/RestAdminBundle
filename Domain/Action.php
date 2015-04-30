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

    private $options = array();

    /**
     * @param $name
     * @param $options
     */
    public function __construct($name, $options)
    {
        $this->name    = $name;
        $this->options = $options;
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
    public function getOption($key, $default = null)
    {
        return isset($this->options[$key]) ? $this->options[$key] : $default;
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->options['form'];
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->getOption('role');
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->getOption('template');
    }

    /**
     * @return string
     */
    public function getController()
    {
        return $this->getOption('controller');
    }
}