<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Loader;

use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\DirectoryResource;

/**
 * Load resources from controller directories
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AnnotationDirectoryLoader extends Loader
{
    /**
     * @var AnnotationFileLoader
     */
    private $loader;
    /**
     * @var FileLocatorInterface
     */
    private $locator;


    public function __construct(
        FileLocatorInterface $locator,
        AnnotationFileLoader $loader
    ) {
        $this->locator = $locator;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $resources = new ResourceCollection();
        $resources->addConfigResource(new DirectoryResource($path));

        $iterator = new \DirectoryIterator($path);
        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() != 'php' || $file->isDir()) {
                continue;
            }

//            var_dump($file->getRealPath());
            $import = $this->loader->load($file->getRealPath());
            $resources->merge($import);
        }

        return $resources;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        try {
            $path = $this->locator->locate($resource);
        } catch (\Exception $e) {
            return false;
        }

        return is_string($resource) && is_dir($path) && (!$type || 'annotation' === $type);
    }
}