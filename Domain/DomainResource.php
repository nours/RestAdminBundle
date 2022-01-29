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

use DomainException;
use InvalidArgumentException;
use Nours\RestAdminBundle\Util\Inflector;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * DomainResource representation.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DomainResource
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
     * @var DomainResource|null
     */
    private $parent = false;

    /**
     * Children resources (resolved after loading).
     *
     * @var DomainResource[]
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
    public function __construct(string $class, array $configs)
    {
        $this->class = $class;

        if (isset($configs['name'])) {
            $this->name = $configs['name'];
            unset($configs['name']);
        } else {
            $exploded = explode("\\", $class);
            $this->name = Inflector::tableize(end($exploded));
        }

        // Single flag
        if (!isset($configs['single'])) {
            $configs['single'] = false;
        }

        // Set default slug
        if (isset($configs['slug'])) {
            $this->slug = $configs['slug'];
            unset($configs['slug']);
        } else {
            $this->slug = str_replace('_', '-', $this->name);

            // Default is pluralized if resource is not single
            if (!$configs['single']) {
                $this->slug = Inflector::pluralize($this->slug);
            }
        }

        $this->configs = $configs;

        $this->basePrefix = $this->getConfig('route_prefix', $this->name) . '_';
        $this->routePrefix = $this->basePrefix;
    }

    /**
     * @param string $newName
     * @param array $configs
     * @return DomainResource
     */
    public function duplicate(string $newName, array $configs = array()): DomainResource
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
     */
    public function setConfig($name, $value)
    {
        $this->configs[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function getConfig(string $name, $default = null)
    {
        return $this->configs[$name] ?? $default;
    }

    /**
     * @return array
     */
    public function getConfigs(): array
    {
        return $this->configs;
    }

    /**
     * @return string|null
     */
    public function getFactory(): ?string
    {
        return $this->getConfig('factory');
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string|array
     */
    public function getIdentifier()
    {
        return $this->getConfig('identifier', 'id');
    }

    /**
     * If this resource's entity has a composite identifier (represented as an array).
     *
     * @return bool
     */
    public function isIdentifierComposite(): bool
    {
        return is_array($this->getIdentifier());
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Full name, including parent paths.
     *
     * @return string
     */
    public function getFullName(): string
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
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return string
     */
    public function getParentName(): ?string
    {
        return $this->getConfig('parent');
    }

    /**
     * @return DomainResource
     */
    public function getParent()
    {
        if ($this->parent === false) {
            throw new DomainException("The parent resource of {$this->getName()} is not resolved yet");
        }
        return $this->parent;
    }

    /**
     * @param DomainResource|null $parent
     */
    public function setParent(DomainResource $parent = null)
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
    public function getForm(): ?string
    {
        return $this->getConfig('form');
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
    {
        return $this->getConfig('role');
    }

    /**
     * @return boolean
     */
    public function isSingleResource(): bool
    {
        return $this->getConfig('single', false);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAction(string $name): bool
    {
        return isset($this->actions[$name]);
    }

    /**
     * @param string $name
     *
     * @return Action
     */
    public function getAction(string $name): ?Action
    {
        if (!$this->hasAction($name)) {
            throw new InvalidArgumentException(sprintf(
                "The action %s is not registered in resource %s (actions : %s)",
                $name, $this->getFullName(), implode(', ', array_keys($this->actions))
            ));
        }
        return $this->actions[$name] ?? null;
    }

    /**
     * @return Action[]
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * Retourne les actions disponibles pour chaque objet.
     *
     * @param array $names
     * @return array
     */
    public function getActionList(array $names): array
    {
        $results = array();

        foreach ($names as $name) {
            if ($action = $this->getAction($name)) {
                $results[] = $action;
            } else {
                throw new DomainException("Action $name not found");
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
    public function getRoutePrefix(): string
    {
        return $this->routePrefix;
    }

    /**
     * Get route name for an action. The suffix should be action name.
     *
     * @param string $routeSuffix
     *
     * @return string
     */
    public function getRouteName(string $routeSuffix): string
    {
        return $this->routePrefix . $routeSuffix;
    }

    /**
     * @param string $name
     *
     * @return DomainResource
     */
    public function getChild(string $name): DomainResource
    {
        if (!isset($this->children[$name])) {
            throw new InvalidArgumentException(sprintf(
                "Resource %s has no child resource %s",
                $this->getFullName(), $name
            ));
        }
        return $this->children[$name];
    }

    /**
     * @param string $name
     *
     * @return boolean
     */
    public function hasChild(string $name): bool
    {
        return isset($this->children[$name]);
    }

    /**
     * @return DomainResource[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Get the relative uri path for this resource.
     *
     * @param string|null $suffix
     * @param boolean $instance If the object param is to be included
     *
     * @return string
     */
    public function getBaseUriPath(string $suffix = null, bool $instance = false): string
    {
        $parts = array();

        if ($parent = $this->getParent()) {
            $parts[] = $parent->getInstanceUriPath();
        }

        if ($path = $this->getConfig('base_path')) {
            $parts[] = $path;
        }

        $parts[] = $this->getSlug();

        // Append the object parameter(s)
        // Always skipped if resource is single
        if ($instance && !$this->isSingleResource()) {
            foreach ($this->getIdentifierNames() as $name) {
                $parts[] = '{' . $name . '}';
            }
        }

        if ($suffix) {
            $parts[] = $suffix;
        }

        return implode('/', $parts);
    }

    /**
     * Get the relative uri path including identification parameters
     *
     * @param null $suffix
     * @return string
     */
    public function getInstanceUriPath($suffix = null): string
    {
        if (null !== $suffix) {
            $suffix = str_replace('_', '-', Inflector::tableize($suffix));
        }

        return $this->getBaseUriPath($suffix, true);
    }

    /**
     * Base route params for this resource. The current resource object id is not included in the params.
     *
     * @param mixed $data
     *
     * @return array
     */
    public function getBaseRouteParams($data = null): array
    {
        if (null !== $data) {
            $parent = $this->getParent();

            if ($this->isResourceInstance($data)) {
                if ($parent) {
                    return $parent->getInstanceRouteParams($this->getParentObject($data));
                }
            } elseif ($parent) {
                if ($parent->isResourceInstance($data)) {
                    return $parent->getInstanceRouteParams($data);
                } else {
                    throw new InvalidArgumentException(sprintf(
                        "Invalid data of type %s (%s or %s expected)",
                        get_class($data), $this->getClass(), $parent->getClass()
                    ));
                }
            }
        }

        return array();
    }

    /**
     * Route params including a resource instance's id.
     *
     * @param mixed $data
     *
     * @return array
     */
    public function getInstanceRouteParams($data): array
    {
        if ($this->isSingleResource()) {
            // Parent must be set for single resources
            $parent = $this->getParent();
            if (empty($parent)) {
                throw new DomainException(sprintf(
                    'Resource %s is single and must be a child of another resource.', $this->getFullName()
                ));
            }

            return $parent->getInstanceRouteParams($this->getParentObject($data));
        } else {
            $params = $this->getBaseRouteParams($data);

            foreach ($this->getIdentifierValues($data) as $paramName => $value) {
                $params[$paramName] = $value;
            }

            return $params;
        }
    }

    /**
     * Build route parameters for a collection of this resource
     *
     * @param array $data
     * @return array
     */
    public function getCollectionRouteParams(array $data): array
    {
        $params = array();

        foreach ((array)$this->getIdentifier() as $identifier) {
            $params[$identifier] = array();
        }

        foreach ($data as $entity) {
            foreach ($this->extractIdentifiers($entity) as $identifier => $value) {
                $params[$identifier][] = $value;
            }
        }

        if ($parent = $this->getParent()) {
            // Use first item of collection
            $object = reset($data);

            $params = array_merge($params, $parent->getInstanceRouteParams($this->getParentObject($object)));
        }

        return $params;
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
     * Returns the param name used in routing
     *
     * @return array
     */
    public function getIdentifierNames(): array
    {
        $paramName = $this->getParamName();
        $names = array();

        if ($this->isIdentifierComposite()) {
            foreach ($this->getIdentifier() as $identifier) {
                $names[$identifier] = $paramName . '_' . $identifier;
            }
        } else {
            $names[$this->getIdentifier()] = $paramName;
        }

        return $names;
    }

    /**
     * @param $data
     * @return bool
     */
    public function isResourceInstance($data): bool
    {
        return $data instanceof $this->class;
    }

    /**
     * Retrieve parent object from data.
     *
     * @param $data
     * @return null|mixed
     */
    public function getParentObject($data)
    {
        if ($data && ($parent = $this->getParent())) {
            if ($parent->isResourceInstance($data)) {
                return $data;
            } elseif ($this->isResourceInstance($data)) {
                return $this->getPropertyAccessor()->getValue($data, $this->getParentPropertyPath());
            } else {
                throw new InvalidArgumentException(sprintf(
                    "Invalid data of type %s (%s or %s expected)",
                    get_class($data), $this->getClass(), $parent->getClass()
                ));
            }
        }

        return null;
    }

    /**
     * Retrieve child object (single resources only)
     *
     * @param $parent
     * @return null|mixed
     */
    public function getSingleChildObject($parent)
    {
        if (!$this->isSingleResource()) {
            return null;
        }

        return $this->getPropertyAccessor()->getValue($parent, $this->getSingleChildPath());
    }

    /**
     * Returns the param => value used in routing
     *
     * @param mixed $data
     * @return array
     */
    private function getIdentifierValues($data): array
    {
        $paramName = $this->getParamName();
        $values = array();

        if ($this->isIdentifierComposite()) {
            foreach ((array)$this->getIdentifier() as $identifier) {
                $values[$paramName . '_' . $identifier] = $this->getPropertyAccessor()->getValue($data, $identifier);
            }
        } else {
            $values[$paramName] = $this->getPropertyAccessor()->getValue($data, $this->getIdentifier());
        }

        return $values;
    }

    /**
     * Extract identifiers from object as an array
     *
     * @param $data
     * @return array
     */
    private function extractIdentifiers($data): array
    {
        $values = array();

        foreach ((array)$this->getIdentifier() as $identifier) {
            $values[$identifier] = $this->getPropertyAccessor()->getValue($data, $identifier);
        }

        return $values;
    }

    /**
     * @return PropertyAccessor
     */
    private function getPropertyAccessor(): PropertyAccessor
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
     *
     * @return array
     */
    public function getPrototypeRouteParams(bool $includeSelf = false): array
    {
        $params = array();

        if ($includeSelf) {
            if ($this->isIdentifierComposite()) {
                foreach ($this->getIdentifierNames() as $identifier => $paramName) {
                    $params[$paramName] = '__' . $this->getName() . '_' . $identifier . '__';
                }
            } else {
                $params[$this->getParamName()] = '__' . $this->getName() . '__';
            }
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
     *
     * @return array
     */
    public function getPrototypeParamsMapping(bool $includeSelf = false): array
    {
        $mapping = array();

        if ($includeSelf) {
            $mapping = $this->makePrototypeParamsMapping();
        }

        $parent = $this->getParent();
        $suffix = '';
        while ($parent) {
            $suffix = $suffix . $this->getParentPropertyPath() . '.';

            $mapping = array_merge($mapping, $parent->makePrototypeParamsMapping($suffix));

            $parent = $parent->getParent();
        }

        return $mapping;
    }

    private function makePrototypeParamsMapping($suffix = ''): array
    {
        $mapping = array();

        if ($this->isIdentifierComposite()) {
            foreach ($this->getIdentifierNames() as $identifier => $paramName) {
                $mapping['__' . $this->getName() . '_' . $identifier . '__'] = $suffix . $identifier;
            }
        } else {
            $mapping['__' . $this->getName() . '__'] = $suffix . $this->getIdentifier();
        }

        return $mapping;
    }

    /**
     * Returns a map associating property paths to route params for routing.
     *
     * @return array
     */
    public function getRouteParamsMapping(): array
    {
        $mapping = $this->makeRouteParamsMapping();

        $parent = $this->getParent();
        $suffix = '';
        while ($parent) {
            $suffix = $suffix . $this->getParentPropertyPath() . '.';
            $mapping = array_merge($mapping, $parent->makeRouteParamsMapping($suffix));

            $parent = $parent->getParent();
        }

        return $mapping;
    }

    private function makeRouteParamsMapping($suffix = ''): array
    {
        $mapping = array();

        if ($this->isIdentifierComposite()) {
            foreach ($this->getIdentifierNames() as $identifier => $paramName) {
                $mapping[$paramName] = $suffix . $identifier;
            }
        } else {
            $mapping[$this->getParamName()] = $suffix . $this->getIdentifier();
        }

        return $mapping;
    }

    public function getParentPropertyPath(): ?string
    {
        // Property path custom definition
        $propertyPath = $this->getConfig('parent_property_path');

        if (null === $propertyPath) {
            // Use parent_path deprecated parameter
            if ($parent = $this->getParent()) {
                $propertyPath = $this->getConfig('parent_path');

                if ($propertyPath) {
                    // parent_path parameter deprecation
                    trigger_error(sprintf(
                        'parent_path resource parameter is deprecated (resource %s), use parent_property_path in %s',
                        $parent->getFullName(), $this->getFullName()
                    ), E_USER_DEPRECATED);
                } else {
                    // Defaults to parent's name
                    $propertyPath = $parent->getName();
                }
            }
        }

        return $propertyPath;
    }

    /**
     * Path for this resource's instance from its parent.
     *
     * @return string
     */
    public function getSingleChildPath(): ?string
    {
        if (!$this->isSingleResource()) {
            return null;
        }

        return $this->getConfig('single_path', $this->getName());
    }

    /**
     * @param $data
     * @return array
     */
    public function getObjectIdentifiers($data): array
    {
        $values = array();

        foreach ((array)$this->getIdentifier() as $identifier) {
            $values[$identifier] = $this->getPropertyAccessor()->getValue($data, $identifier);
        }

        return $values;
    }
}