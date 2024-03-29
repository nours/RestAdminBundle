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

use InvalidArgumentException;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Nours\RestAdminBundle\Domain\ResourceCollectionDumper;
use Nours\RestAdminBundle\EventListener\ParamFetcherListener;
use Nours\RestAdminBundle\EventListener\RequestListener;
use Nours\RestAdminBundle\EventListener\SecurityListener;
use Nours\RestAdminBundle\EventListener\ViewListener;
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
    private $resources = [];

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

    private $dumper;

    /**
     * @param LoaderInterface $loader
     * @param mixed $resource
     * @param string $cacheDir
     * @param bool $debug
     */
    public function __construct(LoaderInterface $loader, $resource, string $cacheDir, bool $debug)
    {
        $this->loader   = $loader;
        $this->resource = $resource;
        $this->cacheDir = $cacheDir;
        $this->debug    = $debug;

        $this->dumper = new ResourceCollectionDumper();
    }

    /**
     * @return ResourceCollection
     */
    public function getResourceCollection()
    {
        $classCacheName = 'appRestResourceCollection';

        if (empty($this->resources)) {
            $filePath = $this->cacheDir. DIRECTORY_SEPARATOR . $classCacheName . '.php';
            $cache = new ConfigCache($filePath, $this->debug);

            if (!$cache->isFresh()) {
                /** @var ResourceCollection $collection */
                $collection = $this->loader->load($this->resource);

                $cache->write($this->dumper->dump($collection, $classCacheName), $collection->getConfigResources());

                $this->resources = $collection;
            } else {

                require_once $filePath;

                $this->resources = new $classCacheName;
            }

            $this->resources->resolveParents();
        }

        return $this->resources;
    }

    /**
     * @param string $name
     *
     * @return DomainResource
     */
    public function getResource(string $name): DomainResource
    {
        return $this->getResourceCollection()->get($name);
    }

    /**
     * Returns an action from fully qualified name.
     *
     * @param string $name
     *
     * @return Action
     */
    public function getAction(string $name): Action
    {
        $exploded = explode(':', $name, 2);

        $resource = $this->getResource($exploded[0]);
        $action = $resource->getAction($exploded[1]);

        if (empty($action)) {
            throw new InvalidArgumentException(sprintf(
                "Action %s not found for resource %s",
                $exploded[1], $exploded[0]
            ));
        }

        return $action;
    }

//    /**
//     * Returns the final resource name, including parent namespaces.
//     *
//     * @param DomainResource $resource
//     * @return string
//     */
//    protected function getResourceName(DomainResource $resource): string
//    {
//        foreach ($this->getParentsIterator($resource) as $parent) {
//            $parts[] = $parent->getName();
//        }
//
//        $parts[] = $resource->getName();
//
//        return implode('.', $parts);
//    }

//    /**
//     * Returns an iterator throughout resource parents from ancestor to immediate parent.
//     *
//     * @param DomainResource $resource
//     * @return \ArrayIterator|DomainResource[]
//     */
//    protected function getParentsIterator(DomainResource $resource)
//    {
//        $parents = [];
//        $current = $resource;
//
//        while ($parent = $current->getParent()) {
//            $parents[] = $parent;
//
//            $current = $parent;
//        }
//
//        return new \ArrayIterator(array_reverse($parents));
//    }

    /**
     * {@inheritdoc}
     */
    public function warmUp(string $cacheDir): array
    {
        $currentDir = $this->cacheDir;

        // force cache generation
        $this->cacheDir = $cacheDir;
        $this->getResourceCollection();

        $this->cacheDir = $currentDir;

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function isOptional(): bool
    {
        return false;
    }
}