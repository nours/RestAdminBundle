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
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Doctrine ORM param fetcher implementation
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineParamFetcher implements ParamFetcherInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * A resource is single if request has attributes for it's parameters.
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return bool
     */
    private function isSingleResource(Request $request, Resource $resource)
    {
        $isSingle = true;

        foreach ($resource->getIdentifierNames() as $paramName) {
            if (!$request->attributes->has($paramName)) {
                $isSingle = false;
            }
        }

        return $isSingle;
    }

    /**
     * @param Request $request
     */
    public function fetch(Request $request)
    {
        /** @var \Nours\RestAdminBundle\Domain\Resource $resource */
        $resource = $request->attributes->get('resource');
        $action   = $request->attributes->get('action');

        if ($this->isSingleResource($request, $resource)) {
            // Request has a resource parameter : it should be fetched
            $finder = $action->getConfig('finder', 'find');

            $data = $this->fetchSingle($request, $resource, $finder);

            $request->attributes->set('data', $data);
        } else {
            // Load collection if request has id parameter in query string
            $identifiers = $resource->getIdentifier();
            $isCollection = true;
            foreach ((array)$identifiers as $identifier) {
                if (!$request->query->has($identifier)) {
                    $isCollection = false;
                }
            }
            if ($isCollection) {
                $data = $this->fetchCollection($request, $resource);

                $request->attributes->set('data', $data);
            }

            // Request may concern a collection with a parent
            if ($parentResource = $resource->getParent()) {
                // If the resource has a parent, fetch it
                $parent = $this->fetchSingle($request, $parentResource);

                $request->attributes->set('parent', $parent);
            }
        }
    }


    /**
     * Fetches data for a resource.
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param string $finderMethod
     * @return mixed
     */
    protected function fetchSingle(Request $request, Resource $resource, $finderMethod = 'find')
    {
        if ($resource->getParent()) {
            $data = $this->findHierarchy($request, $resource);
        } else {
            $data = $this->findSingle($request, $resource, $finderMethod);
        }

        if (empty($data)) {
            // Data must be found, otherwise throw
            throw new NotFoundHttpException();
        }

        return $data;
    }

    /**
     * Finds a single resource (without parent) from request, eventually using action finder.
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param string $finderMethod
     * @return mixed
     */
    private function findSingle(Request $request, Resource $resource, $finderMethod)
    {
        // Composite identifier case
        if ($resource->isCompositeIdentifier()) {
            $criteria = array();

            foreach ($resource->getIdentifierNames() as $identifier => $paramName) {
                $criteria[$identifier] = $request->attributes->get($paramName);
            }

            return $this->manager->getRepository($resource->getClass())->findOneBy($criteria);
        }

        // Single id
        $resourceId = $request->attributes->get($resource->getParamName());

        if ($resourceId) {
            return $this->manager->getRepository($resource->getClass())->$finderMethod($resourceId);
        }

        return null;
    }

    /**
     * Finds a resource having parent relationship.
     *
     * Builds a query in order to ensure parent ownership (it's children may have duplicated ids).
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
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

        // Sets the resource parameter
        $builder->setParameter($resource->getName(), $request->attributes->get($resource->getParamName()));

        $this->buildQueryForParent($builder, $request, $resource);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * Fetches a collection for the resource.
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return array
     */
    protected function fetchCollection(Request $request, Resource $resource)
    {
        /** @var QueryBuilder $builder */
        $builder = $this->manager->getRepository($resource->getClass())
            ->createQueryBuilder('r')
        ;

        $identifier = $resource->getIdentifier();
        $ids = $request->query->get($identifier);

        if (empty($ids)) {
            // Must fetch at least one object
            throw new NotFoundHttpException();
        }

        $builder->where($builder->expr()->in(
            'r.'.$identifier,
            $ids
        ));

        if ($resource->getParent()) {
            $this->buildQueryForParent($builder, $request, $resource);
        }

        $collection = $builder->getQuery()->execute();

        if (count($collection) != count($ids)) {
            // Item count must match
            throw new NotFoundHttpException();
        }

        return $collection;
    }

    /**
     * Builds the query builder to select and filter on resource parent.
     *
     * Resource must have parent
     *
     * @param QueryBuilder $builder
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     */
    private function buildQueryForParent(QueryBuilder $builder, Request $request, Resource $resource)
    {
        $parentAlias = 'r';
        $index = 1;
        $parent = $resource->getParent();
        while ($parent) {
            $alias = 'p'.$index;
            $builder->addSelect($alias)->innerJoin($parentAlias.'.'.$resource->getParentAssociation(), $alias);

            foreach ($parent->getIdentifierNames() as $identifier => $paramName) {
                $builder->andWhere($alias.'.'.$identifier.' = :'.$paramName);
                $builder->setParameter($paramName, $request->attributes->get($paramName));
            }

            $parentAlias = $alias;
            ++$index;
            $resource = $parent;
            $parent   = $parent->getParent();
        }
    }
}