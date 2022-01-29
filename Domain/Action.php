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

use Nours\RestAdminBundle\Util\Inflector;
use SplPriorityQueue;

/**
 * Resource action description.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Action
{
    /**
     * @var DomainResource
     */
    private $resource;

    /**
     * @var array
     */
    private $config;

    /**
     * @param DomainResource $resource
     * @param array $config
     */
    public function __construct(DomainResource $resource, array $config)
    {
        $this->resource = $resource;
        $this->config   = $config;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getConfig('name');
    }

    /**
     * Resource full name prepended to this action's name.
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getResource()->getFullName() . ':' . $this->getName();
    }

    /**
     * @return bool
     */
    public function hasInstance(): bool
    {
        return $this->getConfig('instance');
    }

    /**
     * @return bool
     */
    public function isBulk(): bool
    {
        return $this->getConfig('bulk');
    }

    /**
     * @return DomainResource
     */
    public function getResource(): DomainResource
    {
        return $this->resource;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->getConfig('type');
    }

    /**
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getConfig(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public function setConfig(string $key, $value)
    {
        return $this->config[$key] = $value;
    }

    /**
     * @return array
     */
    public function getConfigs(): array
    {
        return $this->config;
    }

    /**
     * @return string
     */
    public function getForm(): string
    {
        return $this->getConfig('form', $this->getResource()->getForm());
    }

    /**
     * @return string|null
     */
    public function getFactory(): ?string
    {
        return $this->getConfig('factory');
    }

    /**
     * @return string|null
     */
    public function getRole(): ?string
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
    public function isReadOnly(): bool
    {
        return $this->getConfig('read_only');
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->getConfig('template');
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->getConfig('controller');
    }

    /**
     * @return array
     */
    public function getHandlers(): array
    {
        $handlers = $this->getConfig('handlers', array());

        $priorityQueue = new SplPriorityQueue();
        foreach ($handlers as $handler) {
            $priorityQueue->insert($handler[0], $handler[1]);
        }

        return iterator_to_array($priorityQueue);
    }

    /**
     * @param string|null $name
     *
     * @return string
     */
    public function getRouteName(string $name = null): string
    {
        if ($name) {
            trigger_error('Overriding action route name is deprecated, use getFormActionRouteName instead', E_USER_DEPRECATED);
        }

        return $this->resource->getRouteName($name ?: $this->getName());
    }

    /**
     * Returns the route name suffix for the form action
     *
     * @return string
     */
    public function getFormActionRouteSuffix(): string
    {
        return $this->getConfig('form_action_route_suffix', $this->getName());
    }

    /**
     * Returns the route name for the form action
     *
     * @return string
     */
    public function getFormActionRouteName(): string
    {
        return $this->resource->getRouteName($this->getFormActionRouteSuffix());
    }

    /**
     * Generate action route parameters.
     *
     * @param mixed $data
     * @return array
     */
    public function getRouteParams($data = null): array
    {
        $resource = $this->resource;

        if ($this->isBulk()) {
            if (is_array($data)) {
                return $resource->getCollectionRouteParams($data);
            }
        } elseif ($this->hasInstance()) {
            return $resource->getInstanceRouteParams($data);
        }

        return $resource->getBaseRouteParams($data);
    }

    /**
     * Duplicate this action for another resource.
     *
     * @param DomainResource $resource
     * @return Action
     */
    public function duplicate(DomainResource $resource): Action
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
    public function getUriPath($suffix = null): string
    {
        if (is_null($suffix)) {
            // Default suffix is action param name
            $suffix = str_replace('_', '-', Inflector::tableize($this->getName()));
        }
        return $this->getResource()->getBaseUriPath($suffix, $this->hasInstance());
    }

    /**
     * Default route parameters for action prototypes
     *
     * @return array
     */
    public function getPrototypeRouteParams(): array
    {
        return $this->getResource()->getPrototypeRouteParams($this->hasInstance());
    }

    /**
     * Returns an array containing the param mapping for this action, ex :
     *
     * The mapping is relative to this action's instance is it has one. Otherwise, it is relative to it's parent
     * resource instance.
     *
     * @return array
     */
    public function getPrototypeParamsMapping(): array
    {
        $resource = $this->getResource();

        if ($this->hasInstance()) {
            return $resource->getPrototypeParamsMapping(true);
        } elseif ($parentResource = $resource->getParent()) {
            return $parentResource->getPrototypeParamsMapping(true);
        }

        return array();
    }
}