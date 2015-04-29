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


/**
 * Class ResourceCollection
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var Resource[]
     */
    private $resources = array();

    /**
     * Adds a resource, after resolving it's parent.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param null $parent
     */
    public function add(Resource $resource, $parent = null)
    {
        if ($parent) {
            $parent = $this->get($parent);
            $resource->setParent($parent);
        }

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
}