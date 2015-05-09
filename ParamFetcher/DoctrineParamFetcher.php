<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\ParamFetcher;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Doctrine param fetcher implementation
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineParamFetcher
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @param Request $request
     */
    public function fetchParams(Request $request)
    {
        $resource = $request->attributes->get('resource');
        $action   = $request->attributes->get('action');

        $resourceId = $request->attributes->get($resource->getName());

        if (empty($resourceId)) {
            // No resource parameter is set, but it's parent should
            if ($parent = $resource->getParent()) {
                $data = $this->fetch($request, $parent);

                $request->attributes->set('parent', $data);
            }
        } else {
            // Search resource data
            $data = $this->fetch($request, $resource, $action);

            $request->attributes->set('data', $data);
        }
    }


    /**
     * Fetches data for one resource.
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @return mixed
     */
    protected function fetch(Request $request, Resource $resource, Action $action = null)
    {
        if ($resource->getParent()) {
            return $this->findHierarchy($request, $resource);
        } else {
            return $this->findSingle($request, $resource, $action);
        }
    }

    /**
     * Finds a single resource (without parent) from request, eventually using action finder.
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @return mixed
     */
    private function findSingle(Request $request, Resource $resource, Action $action = null)
    {
        $resourceId = $request->attributes->get($resource->getName());

        $finder = $action ? $action->getConfig('finder', 'find') : 'find';

        return $this->manager->getRepository($resource->getClass())->$finder($resourceId);
    }

    /**
     * Finds a resource having parent relationship.
     *
     * Builds a query in order to ensure parent ownership (it's children may have duplicated ids).
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function findHierarchy(Request $request, Resource $resource)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->manager->getRepository($resource->getClass())
            ->createQueryBuilder('r')
            ->where('r.'.$resource->getIdentifier().' = :'.$resource->getName());

        $parameters = array(
            $resource->getName() => $request->attributes->get($resource->getName())
        );

        $parentAlias = 'r';
        $index = 1;
        $parent = $resource;
        while ($parent = $parent->getParent()) {
            $alias = 'p'.$index;
            $builder->addSelect($alias)->innerJoin($parentAlias.'.'.$parent->getName(), $alias)
                ->andWhere($alias.'.'.$parent->getIdentifier().' = :'.$parent->getName());

            $parameters[$parent->getName()] = $request->attributes->get($parent->getName());

            $parentAlias = $alias;
            ++$index;
        }

        $builder->setParameters($parameters);

        return $builder->getQuery()->getSingleResult();
    }

    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return bool
     */
    public function supports(Resource $resource)
    {
        return $this->manager->getMetadataFactory()->hasMetadataFor($resource->getClass());
    }
}