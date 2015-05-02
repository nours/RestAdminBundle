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

use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\FileLoader;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Parser;

/**
 * Load resources from yaml files
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class YamlResourceLoader extends FileLoader
{
    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var ActionFactory
     */
    private $actionFactory;

    public function __construct(FileLocatorInterface $locator, ActionFactory $actionFactory)
    {
        parent::__construct($locator);

        $this->actionFactory = $actionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        if (!file_exists($path)) {
            throw new \InvalidArgumentException(sprintf('File "%s" not found.', $path));
        }

        $configs = $this->getYamlParser()->parse(file_get_contents($path));

        if (empty($configs)) {
            return array();
        }

        $resources = new ResourceCollection();
        $resources->addConfigResource(new FileResource($path));

        // Process resources
        if (isset($configs['resources'])) {
            foreach ($configs['resources'] as $name => $config) {
                $resource = $this->loadResource($name, $config);

                $resources->add($resource);
            }
        }

        // Process imports
        if (isset($configs['imports'])) {
            foreach ($configs['imports'] as $name => $config) {
//                var_dump($config['resource'], self::$loading);
                self::$loading = array();
                $import = $this->import($config['resource'], isset($config['type']) ? $config['type'] : null);

                $resources->merge($import);
            }
        }

        return $resources;
    }

    /**
     * @param string $name
     * @param array $config
     * @return Resource
     */
    protected function loadResource($name, array $config)
    {
        $resource = new Resource($name, $config);

        $this->actionFactory->configureActions($resource, $config);

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) &&
            'yml' === pathinfo($resource, PATHINFO_EXTENSION) &&
            (!$type || 'yaml' === $type);
    }

    /**
     * @return Parser
     */
    private function getYamlParser()
    {
        if (null === $this->parser) {
            $this->parser = new Parser();
        }

        return $this->parser;
    }
}