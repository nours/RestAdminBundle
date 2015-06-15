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
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * Resource description.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Resource
{
    /**
     * @var PropertyAccessor
     */
    private static $propertyAccessor;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var array
     */
    private $configs;

    /**
     * @var Action[]
     */
    private $actions = array();

    /**
     * false means that the parent resource has not been resolved yet.
     *
     * @var Resource|null
     */
    private $parent = false;

    /**
     * @var string
     */
    private $routePrefix;

    /**
     * @var string
     */
    private $basePrefix;

    /**
     * @param string $class
     * @param array $configs
     */
    public function __construct($class, array $configs)
    {
        $this->class = $class;

        if (isset($configs['name'])) {
            $this->name = $configs['name'];
            unset($configs['name']);
        } else {
            $exploded = explode("\\", $class);
            $this->name = Inflector::tableize(end($exploded));
        }

        // Slug defaults to pluralized name
        if (isset($configs['slug'])) {
            $this->slug = $configs['slug'];
            unset($configs['slug']);
        } else {
            $this->slug = Inflector::pluralize($this->name);
        }

        $this->configs = $configs;

        $this->basePrefix = $this->name . '_';
        $this->routePrefix = $this->basePrefix;
    }

    /**
     * @param string $newName
     * @param array $configs
     * @return Resource
     */
    public function duplicate($newName, array $configs = array())
    {
        $configs = array_merge($this->configs, $configs);
        $configs['name'] = $newName;

        $clone = new self($this->class, $configs);

        foreach ($this->actions as $action) {
            $clone->addAction($action->duplicate($clone));
        }

        return $clone;
    }

    /**
     * Used to override or set a config param
     *
     * @return mixed
     */
    public function setConfig($name, $value)
    {
        $this->configs[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function getConfig($name, $default = null)
    {
        return isset($this->configs[$name]) ? $this->configs[$name] : $default;
    }

    /**
     * @return string|null
     */
    public function getFactory()
    {
        return $this->getConfig('factory');
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->getConfig('identifier', 'id');
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Full name, including parent paths.
     *
     * @return string
     */
    public function getFullName()
    {
        $fullName = $this->name;
        if ($parentName = $this->getParentName()) {
            $fullName = $parentName . '.' . $fullName;
        }

        return $fullName;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getParentName()
    {
        return $this->getConfig('parent');
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function getParent()
    {
        if ($this->parent === false) {
            throw new \DomainException("The parent resource of {$this->getName()} is not resolved yet");
        }
        return $this->parent;
    }

    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $parent
     */
    public function setParent(Resource $parent = null)
    {
        $this->parent = $parent;

        if ($parent) {
            $this->routePrefix = $parent->routePrefix . $this->basePrefix;
        }
    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->getConfig('form');
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAction($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * @param string $name
     * @return Action
     */
    public function getAction($name)
    {
        return isset($this->actions[$name]) ? $this->actions[$name] : null;
    }

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * Get the route prefix
     *
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
    }

    /**
     * Get route name for an action
     *
     * @param string $actionName
     * @return string
     */
    public function getRouteName($actionName)
    {
        return $this->routePrefix . $actionName;
    }


    /**
     * Get the relative uri path for this resource.
     *
     * @param string $suffix
     * @param boolean $objectPart If the object param is to be included
     * @return string
     */
    public function getUriPath($suffix = null, $objectPart = false)
    {
        $parts = array();

        if ($parent = $this->getParent()) {
            $parts[] = $parent->getResourceUriPath();
        }

        $parts[] = $this->getSlug();

        if ($objectPart) {
            $parts[] = '{' . $this->getName() . '}';
        }

        if ($suffix) {
            $parts[] = $suffix;
        }

        return implode('/', $parts);
    }

    /**
     * Get the relative uri path including the identifier param
     *
     * @param null $suffix
     * @return string
     */
    public function getResourceUriPath($suffix = null)
    {
        return $this->getUriPath($suffix, true);
    }

    /**
     * Build route parameters for an instance of this resource
     *
     * @param mixed $data
     * @return array
     */
    public function getRouteParamsFromData($data)
    {
        if ($parent = $this->getParent()) {
            return $parent->getResourceRouteParams($this->getParentObject($data));
        }

        return array();
    }

    /**
     * Returns the route params from the parent object.
     *
     * @param mixed $parent
     * @return array
     */
    public function getRouteParamsFromParent($parent)
    {
        if ($parentResource = $this->getParent()) {
            return $parentResource->getResourceRouteParams($parent);
        }

        return array();
    }

    /**
     * Build route parameters for an instance of this resource
     *
     * @param $data
     * @return array
     */
    public function getResourceRouteParams($data)
    {
        $params = array(
            $this->getName() => $this->getObjectIdentifier($data)
        );

        if ($parent = $this->getParent()) {
            $params = array_merge($params, $parent->getResourceRouteParams($this->getParentObject($data)));
        }

        return $params;
    }

    /**
     * Retrieve parent object from data.
     *
     * @param $data
     * @return null|mixed
     */
    public function getParentObject($data)
    {
        if ($parent = $this->getParent()) {
            return $this->getPropertyAccessor()->getValue($data, $parent->getName());
        }

        return null;
    }

    /**
     * Returns an object id value
     *
     * @param $data
     * @return mixed
     */
    public function getObjectIdentifier($data)
    {
        return $this->getPropertyAccessor()->getValue($data, $this->getIdentifier());
    }


    private function getPropertyAccessor()
    {
        if (empty(self::$propertyAccessor)) {
            self::$propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return self::$propertyAccessor;
    }
}