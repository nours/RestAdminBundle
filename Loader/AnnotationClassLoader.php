<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Loader;

use Doctrine\Common\Annotations\Reader;
use Nours\RestAdminBundle\Annotation\Resource;
use Nours\RestAdminBundle\Annotation\Route;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Config\Resource\FileResource;


/**
 * Loads resource instances from controller classes.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AnnotationClassLoader implements LoaderInterface
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * @var ResourceFactory
     */
    protected $factory;

    /**
     * @var string
     */
    protected $resourceAnnotationClass = 'Nours\RestAdminBundle\\Annotation\\Resource';

    /**
     * @var string
     */
    protected $actionAnnotationClass = 'Nours\RestAdminBundle\\Annotation\\Action';

    /**
     * @var string
     */
    protected $factoryAnnotationClass = 'Nours\RestAdminBundle\\Annotation\\Factory';

    /**
     * @var string
     */
    protected $handlerAnnotationClass = 'Nours\RestAdminBundle\\Annotation\\Handler';

    /**
     * @var string
     */
    protected $routeAnnotationClass = 'Nours\RestAdminBundle\\Annotation\\Route';

    /**
     * Constructor.
     *
     * @param Reader $reader
     * @param ResourceFactory $factory
     */
    public function __construct(Reader $reader, ResourceFactory $factory)
    {
        $this->reader = $reader;
        $this->factory = $factory;
    }

    /**
     * @param mixed $class
     * @param null $type
     * @return ResourceCollection
     */
    public function load($class, $type = null)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $class = new \ReflectionClass($class);
        if ($class->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        $collection = new ResourceCollection();
        $collection->addConfigResource(new FileResource($class->getFileName()));

        if ($annotation = $this->reader->getClassAnnotation($class, $this->resourceAnnotationClass)) {

            $resource = $this->processResource($class, $annotation);

            $collection->add($resource);

            $this->factory->finishResource($resource, $collection);
        }

        return $collection;
    }

    /**
     * @param \ReflectionClass $class
     * @param \Nours\RestAdminBundle\Annotation\Resource $annotation
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    private function processResource(\ReflectionClass $class, Resource $annotation)
    {
        // Look for @Factory annotation
        $factory = null;
        foreach ($class->getMethods() as $method) {
            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if ($annot instanceof $this->factoryAnnotationClass) {
                    $factory = $this->getControllerName($class, $annotation, $method);
                }
            }
        }

        $config = $annotation->config;
        if ($factory) {
            $config['factory'] = $factory;
        }

        $resource = $this->factory->createResource($annotation->class, $config);

        $this->factory->configureActions($resource, $this->processActions($class, $annotation));

        return $resource;
    }

    /**
     * Creates the actions configuration from annotations.
     *
     * @param \ReflectionClass $class
     * @param $resourceAnnotation
     * @return array
     */
    private function processActions(\ReflectionClass $class, $resourceAnnotation)
    {
        $configs = array();

        // @Action class annotations
        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof $this->actionAnnotationClass) {
                if (empty($annotation->name)) {
                    throw new \DomainException(sprintf(
                        "Missing action name from @Action annotation on class %s", $class->getName()
                    ));
                }
                $configs[$annotation->name] = $annotation->options;
            }
        }

        // @Action method annotations
        foreach ($class->getMethods() as $method) {
            if ($annotation = $this->reader->getMethodAnnotation($method, $this->actionAnnotationClass)) {
                // Action name can be ommitted from annotation
                $actionName = $annotation->name ?: $this->guessActionName($method);

                $config = array_merge($annotation->options, array(
                    'controller' => $this->getControllerName($class, $resourceAnnotation, $method)
                ));

                // @Route method annotation
                /** @var Route[] $routes */
                $routes = $this->getMethodAnnotations($method, $this->routeAnnotationClass);
                if ($routes) {
                    foreach ($routes as $route) {
                        $routeConfig = $route->toArray();

                        // Name defaults to action name
                        if (!isset($routeConfig['name'])) {
                            $routeConfig['name'] = $actionName;
                        }

                        // Add the route config to action, assuming it's a default one
                        $config['routes'][] = $routeConfig;
                    }
                }

                $configs[$actionName] = $config;
            }
        }

        // @Handler method annotation
        foreach ($class->getMethods() as $method) {
            foreach ($this->getMethodAnnotations($method, $this->handlerAnnotationClass) as $annotation) {
                $actionName = $annotation->action;

                if (!isset($configs[$actionName])) {
                    throw new \DomainException(sprintf(
                        "Handler method %s::%s is configured for action %s, which is not found for resource (%s are)",
                        $class->getName(), $method->getName(), $actionName, implode(', ', array_keys($configs))
                    ));
                }

                $configs[$actionName]['handlers'][] = array(
                    $this->getControllerName($class, $resourceAnnotation, $method), $annotation->priority ?: 0
                );
            }
        }


        return $configs;
    }

    /**
     * Guess the action name from a controller method (just strip 'Action' from the end of the method name).
     *
     * @param \ReflectionMethod $method
     * @return string
     */
    private function guessActionName(\ReflectionMethod $method)
    {
        return preg_replace('/(\w+)Action/i', '$1', $method->getName());
    }

    /**
     * Gets the name for a controller method.
     *
     * @param $resourceAnnotation
     * @param \ReflectionMethod $method
     * @return string
     */
    private function getControllerName(\ReflectionClass $class, $resourceAnnotation, \ReflectionMethod $method)
    {
        if ($service = $resourceAnnotation->service) {
            // Use service_id:method notation
            return $service . ':' . $method->getName();
        }

        // Use class::method notation
        return $class->getName() . '::' . $method->getName();
    }

    /**
     * Loads annotations of a given type from a method.
     *
     * @param \ReflectionMethod $method
     * @param $type
     * @return array of $type
     */
    private function getMethodAnnotations(\ReflectionMethod $method, $type)
    {
        $found = array();
        foreach ($this->reader->getMethodAnnotations($method) as $annotation) {
            if ($annotation instanceof $type) {
                $found[] = $annotation;
            }
        }

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && class_exists($resource) && (!$type || 'annotation' === $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
    }
}