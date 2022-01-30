<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Domain;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Serializable;
use Symfony\Component\Config\Resource\ResourceInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ResourceCollection
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceCollection implements Countable, IteratorAggregate, Serializable
{
    /**
     * @var DomainResource[]
     */
    private $resources = [];

    /**
     * @var array
     *
     * @Serializer\Exclude()
     */
    private $configResources = [];

    /**
     * Adds a resource, after resolving its parent.
     *
     * @param DomainResource $resource
     */
    public function add(DomainResource $resource)
    {
        $name = $resource->getFullName();
        $this->resources[$name] = $resource;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->resources[$name]);
    }

    /**
     * @param string $name
     *
     * @return DomainResource
     */
    public function get(string $name): DomainResource
    {
        if (!$this->has($name)) {
            throw new InvalidArgumentException("No $name resource registered in this collection");
        }
        return $this->resources[$name];
    }

    /**
     * Resolves parent resource elements
     */
    public function resolveParents()
    {
        foreach ($this->resources as $resource) {
            $this->resolveResourceParent($resource);
        }
    }

    private function resolveResourceParent(DomainResource $resource)
    {
        $parentName = $resource->getParentName();

        // Do nothing if no parent or already resolved
        if (null === $parentName) {
            $resource->setParent(null);
            return;
        }

        $parent = $this->get($parentName);
        $this->resolveResourceParent($parent);

        $resource->setParent($parent);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->resources);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->resources);
    }

    /**
     * @param ResourceInterface $resource
     * @return $this
     */
    public function addConfigResource(ResourceInterface $resource): ResourceCollection
    {
        $this->configResources[] = $resource;
        return $this;
    }

    /**
     * @return ResourceInterface[]
     */
    public function getConfigResources(): array
    {
        return $this->configResources;
    }

    /**
     * Merges another collection in this one.
     *
     * @param self $other
     */
    public function merge(ResourceCollection $other)
    {
        foreach ($other->resources as $resource) {
            $this->add($resource);
        }

        foreach ($other->configResources as $resource) {
            $this->addConfigResource($resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        // Serialize resources
        return serialize($this->resources);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->resources = unserialize($serialized);
        $this->configResources = [];
    }
}