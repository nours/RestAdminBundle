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

use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

/**
 * The admin manager.
 *
 * It holds the collection of resources, and the builders.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminManager implements CacheWarmerInterface
{
    /**
     * @var ResourceCollection
     */
    private $resources = array();

    /**
     * @var mixed
     */
    private $resource;

    /**
     * @var LoaderInterface
     */
    private $loader;
    private $cacheDir;
    private $debug;

    /**
     * @param LoaderInterface $loader
     * @param mixed $resource
     * @param $cacheDir
     * @param $debug
     */
    public function __construct(LoaderInterface $loader, $resource, $cacheDir, $debug)
    {
        $this->loader   = $loader;
        $this->resource = $resource;
        $this->cacheDir = $cacheDir;
        $this->debug    = $debug;
    }

    /**
     * @return ResourceCollection
     */
    public function getResourceCollection()
    {
        if (empty($this->resources)) {
            $filePath = $this->cacheDir.'/RestResourceCollection.php';
            $cache = new ConfigCache($filePath, $this->debug);

            if (!$cache->isFresh()) {
                /** @var ResourceCollection $collection */
                $collection = $this->loader->load($this->resource);

                $export = var_export(serialize($collection), true);
                $cache->write('<?php return unserialize('.$export.');', $collection->getConfigResources());

                $this->resources = $collection;
            } else {
                $this->resources = require $filePath;
            }

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

    /**
     * {@inheritdoc}
     */
    public function warmUp($cacheDir)
    {
        $currentDir = $this->cacheDir;

        // force cache generation
        $this->cacheDir = $cacheDir;
        $this->getResourceCollection();

        $this->cacheDir = $currentDir;
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional()
    {
        return false;
    }
}