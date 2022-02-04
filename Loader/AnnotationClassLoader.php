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
use DomainException;
use InvalidArgumentException;
use Nours\RestAdminBundle\Annotation\Action as ActionAnnotation;
use Nours\RestAdminBundle\Annotation\DomainResource as DomainResourceAnnotation;
use Nours\RestAdminBundle\Annotation\Factory as FactoryAnnotation;
use Nours\RestAdminBundle\Annotation\Handler as HandlerAnnotation;
use Nours\RestAdminBundle\Annotation\ParamFetcher as ParamFetcherAnnotation;
use Nours\RestAdminBundle\Annotation\Route as RouteAnnotation;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Nours\RestAdminBundle\Util\Inflector;
use ReflectionClass;
use ReflectionMethod;
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
    protected $resolver;

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
    public function load($class, $type = null): ResourceCollection
    {
        if (!class_exists($class)) {
            throw new InvalidArgumentException(sprintf('Class "%s" does not exist.', $class));
        }

        $class = new ReflectionClass($class);
        if ($class->isAbstract()) {
            throw new InvalidArgumentException(sprintf('Annotations from class "%s" cannot be read as it is abstract.', $class));
        }

        $collection = new ResourceCollection();
        $collection->addConfigResource(new FileResource($class->getFileName()));

        if ($annotation = $this->reader->getClassAnnotation($class, DomainResourceAnnotation::class)) {
            $resource = $this->processResource($class, $annotation);

            $collection->add($resource);

            $this->factory->finishResource($resource, $collection);
        }

        return $collection;
    }

    /**
     *
     * @param ReflectionClass $class
     * @param DomainResourceAnnotation $annotation
     * @return DomainResource
     */
    private function processResource(ReflectionClass $class, DomainResourceAnnotation $annotation): DomainResource
    {
        $resourceFactory = null;
        $fetcher = null;
        foreach ($class->getMethods() as $method) {
            // Look for @Factory annotation
            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if (($annot instanceof FactoryAnnotation) && (null === $annot->action)) {
                    $resourceFactory = $this->getControllerName($class, $annotation, $method);
                }
            }

            // Look for @ParamFetcher annotation for the resource (its action param is not set)
            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if ($annot instanceof ParamFetcherAnnotation && null === $annot->action) {
                    $fetcher = $this->getControllerName($class, $annotation, $method);
                }
            }
        }

        // Get resource config from annotation
        $config = $annotation->config;

        if ($resourceFactory) {
            $config['factory'] = $resourceFactory;
        }
        if ($fetcher) {
            $config['fetcher'] = 'custom';
            $config['fetcher_callback'] = $fetcher;
        }

        $resource = $this->factory->createResource($annotation->class, $config);

        $this->factory->configureActions($resource, $this->processActions($class, $annotation));

        return $resource;
    }

    /**
     * Creates the actions configuration from annotations.
     *
     * @param ReflectionClass $class
     * @param $resourceAnnotation
     * @return array
     */
    private function processActions(ReflectionClass $class, $resourceAnnotation): array
    {
        $configs = [];

        // @Action class annotations
        foreach ($this->reader->getClassAnnotations($class) as $annotation) {
            if ($annotation instanceof ActionAnnotation) {
                if (empty($annotation->name)) {
                    throw new DomainException(sprintf(
                        "Missing action name from @Action annotation on class %s", $class->getName()
                    ));
                }
                if ($annotation->disabled) {
                    $configs[$annotation->name] = false;
                } else {
                    $configs[$annotation->name] = $annotation->options;
                }
            }
        }

        // @Action method annotations
        foreach ($class->getMethods() as $method) {
            if ($annotation = $this->reader->getMethodAnnotation($method, ActionAnnotation::class)) {
                // Action name can be ommitted from annotation
                $actionName = $annotation->name ?: $this->guessActionName($method);

                if ($annotation->disabled) {
                    $configs[$actionName] = false;
                } else {
                    $config = array_merge($annotation->options, array(
                        'controller' => $this->getControllerName($class, $resourceAnnotation, $method)
                    ));

                    // Custom param fetcher ?
                    if (isset($fetchers[$actionName])) {
                        $config['fetcher'] = 'custom';
                        $config['fetcher_callback'] = $fetchers[$actionName];
                    }

                    // @Route method annotation
                    /** @var array<RouteAnnotation> $routes */
                    $routes = $this->getMethodAnnotations($method, RouteAnnotation::class);
                    if ($routes) {
                        foreach ($routes as $route) {
                            $routeConfig = $route->toArray();

                            // Name defaults to action name
                            if (!isset($routeConfig['name'])) {
                                $routeConfig['name'] = $actionName;
                            }

                            // Add the route config to action, assuming its a default one
                            $config['routes'][] = $routeConfig;
                        }
                    }

                    $configs[$actionName] = $config;
                }
            }
        }

        // Load all @ParamFetcher annotation for a specific action
        foreach ($class->getMethods() as $method) {
            foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                if ($annot instanceof ParamFetcherAnnotation && ($actionName = $annot->action)) {
                    // Check the action was previously found
                    if (!isset($configs[$actionName])) {
                        if (!in_array($actionName, array('index', 'get'))) {
                            throw new DomainException(sprintf(
                                "ParamFetcher annotation bound to unknown action %s in controller %s",
                                $actionName, $class
                            ));
                        }

                        // Initialize config for index or get actions (others must be explicitly defined)
                        $configs[$actionName] = [];
                    }

                    $configs[$actionName]['fetcher'] = 'custom';
                    $configs[$actionName]['fetcher_callback'] = $this->getControllerName(
                        $class, $resourceAnnotation, $method
                    );
                }
            }
        }

        // Other method annotations
        foreach ($class->getMethods() as $method) {
            // @Handler method annotation
            foreach ($this->getMethodAnnotations($method, HandlerAnnotation::class) as $annotation) {
                /** @var HandlerAnnotation $annotation */
                $actionName = $annotation->action;

                if (!isset($configs[$actionName])) {
                    throw new DomainException(sprintf(
                        "Handler method %s::%s is configured for action %s, which is not found for resource (%s are)",
                        $class->getName(), $method->getName(), $actionName, implode(', ', array_keys($configs))
                    ));
                }

                $configs[$actionName]['extra_handlers'][] = array(
                    $this->getControllerName($class, $resourceAnnotation, $method), $annotation->priority ?: 0
                );
            }
        }

        // Other method annotations
        foreach ($class->getMethods() as $method) {
            // @Factory method annotation
            foreach ($this->getMethodAnnotations($method, FactoryAnnotation::class) as $annotation) {
                /** @var FactoryAnnotation $annotation */
                if ($annotation->action) {
                    $actionName = $annotation->action;

                    if (!isset($configs[$actionName])) {
                        throw new DomainException(sprintf(
                            "Factory method %s::%s is configured for action %s, which is not found for resource (%s are)",
                            $class->getName(), $method->getName(), $actionName, implode(', ', array_keys($configs))
                        ));
                    }

                    $configs[$actionName]['factory'] = $this->getControllerName($class, $resourceAnnotation, $method);
                }
            }
        }

        return $configs;
    }

    /**
     * Guess the action name from a controller method (just strip 'Action' from the end of the method name).
     *
     * The CamelCase notation is converted to camel_case.
     *
     * @param ReflectionMethod $method
     * @return string
     */
    private function guessActionName(ReflectionMethod $method): string
    {
        $name = preg_replace('/(\w+)Action/i', '$1', $method->getName());

        return Inflector::tableize($name);
    }

    /**
     * Gets the name for a controller method.
     *
     * @param ReflectionClass $class
     * @param $resourceAnnotation
     * @param ReflectionMethod $method
     *
     * @return string
     */
    private function getControllerName(ReflectionClass $class, $resourceAnnotation, ReflectionMethod $method): string
    {
        if ($service = $resourceAnnotation->service) {
            // Use service_id::method notation
            return $service . '::' . $method->getName();
        }

        // Use class::method notation
        return $class->getName() . '::' . $method->getName();
    }

    /**
     * Loads annotations of a given type from a method.
     *
     * @param ReflectionMethod $method
     * @param class-string<T> $type
     *
     * @return array<T>
     *
     * @template T
     */
    private function getMethodAnnotations(ReflectionMethod $method, string $type): array
    {
        $found = [];
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
    public function supports($resource, $type = null): bool
    {
        return is_string($resource) && class_exists($resource) && (!$type || 'annotation' === $type);
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver(): LoaderResolverInterface
    {
        return $this->resolver;
    }
}