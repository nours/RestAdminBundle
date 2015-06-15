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

use Symfony\Component\Config\Resource\ResourceInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class ResourceCollection
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceCollection implements \Countable, \IteratorAggregate, \Serializable
{
    /**
     * @var Resource[]
     */
    private $resources = array();

    /**
     * @var array
     *
     * @Serializer\Exclude()
     */
    private $configResources = array();

    /**
     * Adds a resource, after resolving it's parent.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     */
    public function add(Resource $resource)
    {
        $name = $resource->getFullName();
        $this->resources[$name] = $resource;
    }

    /**
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->resources[$name]);
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function get($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException("No $name resource registered in this collection");
        }
        return $this->resources[$name];
    }

    /**
     * Resolves parent resource elements
     */
    public function resolveParents()
    {
        $this->resolving = array();
        foreach ($this->resources as $name => $resource) {
            $this->resolveResourceParent($resource);
        }
    }

    private function resolveResourceParent(Resource $resource)
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
    public function count()
    {
        return count($this->resources);
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource[]
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->resources);
    }

    /**
     * @param ResourceInterface $resource
     * @return $this
     */
    public function addConfigResource(ResourceInterface $resource)
    {
        $this->configResources[] = $resource;
        return $this;
    }

    /**
     * @return ResourceInterface[]
     */
    public function getConfigResources()
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
        $this->configResources = array();
    }
}