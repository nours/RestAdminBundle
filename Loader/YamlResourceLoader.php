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
     * @var ResourceFactory
     */
    private $resourceFactory;

    public function __construct(FileLocatorInterface $locator, ResourceFactory $resourceFactory)
    {
        parent::__construct($locator);

        $this->resourceFactory = $resourceFactory;
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

        $resources = new ResourceCollection();
        $resources->addConfigResource(new FileResource($path));

        // Process resources
        if (isset($configs['resources'])) {
            foreach ($configs['resources'] as $name => $config) {
                $resource = $this->loadResource($name, $config);

                $resources->add($resource);

                $this->resourceFactory->finishResource($resource, $resources);
            }
        }

        // Process imports
        if (isset($configs['imports'])) {
            foreach ($configs['imports'] as $name => $config) {
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
        if (!isset($config['class'])) {
            throw new \InvalidArgumentException("Resource class missing for $name");
        }

        $class = $config['class'];
        unset($config['class']);
        $config['name'] = $name;

        $resource = $this->resourceFactory->createResource($class, $config);

        $this->resourceFactory->configureActions($resource, $this->normalizeActionsConfig($config));

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

    /**
     * Extracts and normalize the action configuration
     *
     * @param array $configs
     * @return array
     */
    private function normalizeActionsConfig(array $configs)
    {
        $actions = isset($configs['actions']) ? $configs['actions'] : array();

        foreach ($actions as $name => $config) {
            if (is_string($config)) {
                $actions[$config] = array();
                unset($actions[$name]);
            }
        }

        return $actions;
    }
}