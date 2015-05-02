<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle;

use Nours\RestAdminBundle\Action\ActionBuilderInterface;
use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * The admin manager.
 *
 * It holds the collection of resources, and the builders.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminManager
{
    /**
     * @var ResourceCollection
     */
    private $resources = array();

    /**
     * @var string
     */
    private $resource;

    /**
     * @var
     */
    private $loader;

    /**
     * @param LoaderInterface $loader
     * @param $resource
     */
    public function __construct(LoaderInterface $loader, $resource)
    {
        $this->loader   = $loader;
        $this->resource = $resource;
    }

    /**
     * @return ResourceCollection
     */
    public function getResourceCollection()
    {
        if (empty($this->resources)) {
            $this->resources = $this->loader->load($this->resource);
            $this->resources->resolveParents();
        }

        return $this->resources;
    }

    /**
     * @param string $name
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function getResource($name)
    {
        return $this->getResourceCollection()->get($name);
    }

    /**
     * Returns the final resource name, including parent namespaces.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return string
     */
    protected function getResourceName(Resource $resource)
    {
        foreach ($this->getParentsIterator($resource) as $parent) {
            $parts[] = $parent->getName();
        }

        $parts[] = $resource->getName();

        return implode('.', $parts);
    }

    /**
     * Returns an iterator throughout resource parents from ancestor to immediate parent.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return \Nours\RestAdminBundle\Domain\Resource[]
     */
    protected function getParentsIterator(Resource $resource)
    {
        $parents = array();
        $current = $resource;

        while ($parent = $current->getParent()) {
            $parents[] = $parent;

            $current = $parent;
        }

        return new \ArrayIterator(array_reverse($parents));
    }
}