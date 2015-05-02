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
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Config\Resource\FileResource;


/**
 * Class AnnotationClassLoader
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
     * @var ActionFactory
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
     * Constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader, ActionFactory $factory)
    {
        $this->reader = $reader;
        $this->factory = $factory;
    }


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

            $resource = $this->processResource($annotation);

            foreach ($class->getMethods() as $method) {
                foreach ($this->reader->getMethodAnnotations($method) as $annot) {
                    if ($annot instanceof $this->routeAnnotationClass) {
//                        $this->addRoute($collection, $annot, $globals, $class, $method);
                    }
                }
            }
            $configs = array();

            $this->factory->configureActions($resource, $configs);

            $collection->add($resource);
        }



        return $collection;
    }

    /**
     * @param Resource $annotation
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    private function processResource(Resource $annotation)
    {
        $name = $annotation->name;

        $resource = \Nours\RestAdminBundle\Domain\Resource::create(
            $name,
            $annotation->class,
            $annotation->parent,
            $annotation->identifier,
            $annotation->form
        );

        return $resource;
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