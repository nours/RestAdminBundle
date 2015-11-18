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
     * false means that the parent resource has not been resolved yet.
     *
     * @var Resource[]
     */
    private $children = array();

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
            $this->slug = Inflector::pluralize(str_replace('_', '-', $this->name));
        }

        $this->configs = $configs;

        $this->basePrefix = $this->getConfig('route_prefix', $this->name) . '_';
        $this->routePrefix = $this->basePrefix;
    }

    /**
     * @param string $newName
     * @param array $configs
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function duplicate($newName, array $configs = array())
    {
        $configs = array_merge($this->configs, $configs);
        $configs['name'] = $newName;

        $clone = new static($this->class, $configs);

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
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
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

            $parent->children[$this->getName()] = $this;
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
     * @return string
     */
    public function getRole()
    {
        return $this->getConfig('role');
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
        if (!$this->hasAction($name)) {
            throw new \InvalidArgumentException(sprintf(
                "The action %s is not registered in resource %s (actions : %s)",
                $name, $this->getFullName(), implode(', ', array_keys($this->actions))
            ));
        }
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
     * Retourne les actions disponibles pour chaque objet.
     *
     * @param array $names
     * @return array
     */
    public function getActionList(array $names)
    {
        $results = array();

        foreach ($names as $name) {
            if ($action = $this->getAction($name)) {
                $results[] = $action;
            } else {
                throw new \DomainException("Action $name not found");
            }
        }

        return $results;
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
     * @param string $name
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function getChild($name)
    {
        if (!isset($this->children[$name])) {
            throw new \InvalidArgumentException(sprintf(
                "Resource %s has no child resource %s",
                $this->getFullName(), $name
            ));
        }
        return $this->children[$name];
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasChild($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function getUriPart()
    {

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

        if ($path = $this->getConfig('base_path')) {
            $parts[] = $path;
        }

        $parts[] = $this->getSlug();

        if ($objectPart) {
            $parts[] = '{' . $this->getParamName() . '}';
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
        return $this->getUriPath(str_replace('_', '-', Inflector::tableize($suffix)), true);
    }

    /**
     * Build global route parameters (without the instance) from an instance of this resource.
     *
     * Eg. : pass a comment to get { post: <id> }
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
     * Returns the param name used in routing, and parent retrieval
     *
     * @return mixed
     */
    public function getParamName()
    {
        return $this->getConfig('param_name', $this->name);
    }

    /**
     * @param $data
     * @return bool
     */
    public function isResourceInstance($data)
    {
        return $data instanceof $this->class;
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
            $this->getParamName() => $this->getObjectIdentifier($data)
        );

        if ($parent = $this->getParent()) {
            $params = array_merge($params, $parent->getResourceRouteParams($this->getParentObject($data)));
        }

        return $params;
    }

    /**
     * Build route parameters for a collection of this resource
     *
     * @param array $data
     * @return array
     */
    public function getCollectionRouteParams(array $data)
    {
        $params = array(
            $this->getIdentifier() => array_map(array($this, 'getObjectIdentifier'), $data)
        );

        if ($parent = $this->getParent()) {
            // Use first item of collection
            $object = reset($data);

            $params = array_merge($params, $parent->getResourceRouteParams($this->getParentObject($object)));
        }

        return $params;
    }

    /**
     * Retrieve parent object from data.
     *
     * todo : use a property path different of param name (for underscores in resource name)
     *
     * @param $data
     * @return null|mixed
     */
    public function getParentObject($data)
    {
        if ($parent = $this->getParent()) {
            return $this->getPropertyAccessor()->getValue($data, $parent->getParamName());
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

    /**
     * @return PropertyAccessor
     */
    private function getPropertyAccessor()
    {
        if (empty(self::$propertyAccessor)) {
            self::$propertyAccessor = PropertyAccess::createPropertyAccessor();
        }

        return self::$propertyAccessor;
    }

    /**
     * Route parameters for action prototypes : placeholders are generated.
     *
     * @param bool $includeSelf
     * @return array
     */
    public function getPrototypeRouteParams($includeSelf = false)
    {
        $params = array();

        if ($includeSelf) {
            $params[$this->getParamName()] = '__' . $this->getName() . '__';
        }

        if ($parent = $this->getParent()) {
            $params = array_merge($params, $parent->getPrototypeRouteParams(true));
        }

        return $params;
    }

    /**
     * Returns the map associating placeholders and the property path
     * which will be used to get values from domain objects
     *
     * @param bool $includeSelf
     * @return array
     */
    public function getPrototypeParamsMapping($includeSelf = false)
    {
        $mapping = array();

        if ($includeSelf) {
            $mapping['__' . $this->getName() . '__'] = $this->getIdentifier();
        }

        $parent = $this->getParent();
        $suffix = '';
        while ($parent) {
            $suffix = $suffix . $parent->getParamName() . '.';
            $mapping['__' . $parent->getName() . '__'] = $suffix . $parent->getIdentifier();

            $parent = $parent->getParent();
        }

        return $mapping;
    }

    /**
     * Gets the route params mapping.
     *
     * Currently used for generating reorder urls front side.
     *
     * @return array
     */
    public function getRouteParamsMapping()
    {
        $mapping = array(
            $this->getParamName() => $this->getIdentifier()
        );

        $parent = $this->getParent();
        $suffix = '';
        while ($parent) {
            $suffix = $suffix . $parent->getParamName() . '.';
            $mapping[$parent->getParamName()] = $suffix . $parent->getIdentifier();

            $parent = $parent->getParent();
        }

        return $mapping;
    }
}