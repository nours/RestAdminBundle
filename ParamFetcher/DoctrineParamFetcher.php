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

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Nours\RestAdminBundle\Domain\DomainResource;
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
     * @var EntityManager
     */
    protected $manager;

    public function __construct(EntityManager $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * A resource is single if request has attributes for it's parameters.
     *
     * @param Request $request
     * @param DomainResource $resource
     * @return bool
     */
    private function isSingleInstance(Request $request, DomainResource $resource)
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
        /** @var DomainResource $resource */
        $resource = $request->attributes->get('resource');
        $action   = $request->attributes->get('action');

        if ($resource->isSingleResource()) {
            // Single resource instance are fetched throughout their parents
            if (!$resource->getParent()) {
                throw new \DomainException(sprintf(
                    'Resource %s is single, therefore it must have a parent resource.',
                    $resource->getFullName()
                ));
            }

            $parent = $this->fetchInstance($request, $resource->getParent());
            $request->attributes->set('parent', $parent);
            $request->attributes->set('data', $resource->getSingleChildObject($parent));
        } elseif ($this->isSingleInstance($request, $resource)) {
            // Request has a resource parameter : it should be fetched
            $finder = $action->getConfig('finder', 'find');

            $data = $this->fetchInstance($request, $resource, $finder);

            $request->attributes->set('data', $data);
            $request->attributes->set('parent', $resource->getParentObject($data));
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
                $parent = $this->fetchInstance($request, $parentResource);

                $request->attributes->set('parent', $parent);
            }
        }
    }

    /**
     * Fetches data for a resource.
     *
     * @param Request $request
     * @param DomainResource $resource
     * @param string $finderMethod
     * @return mixed
     */
    protected function fetchInstance(Request $request, DomainResource $resource, $finderMethod = 'find')
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
     * @param DomainResource $resource
     * @param string $finderMethod
     * @return mixed
     */
    private function findSingle(Request $request, DomainResource $resource, $finderMethod)
    {
        // Composite identifier case
        if ($resource->isIdentifierComposite()) {
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
     * @param DomainResource $resource
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    private function findHierarchy(Request $request, DomainResource $resource)
    {
        $builder = $this->manager->getRepository($resource->getClass())
            ->createQueryBuilder('r');

        foreach ($resource->getIdentifierNames() as $identifier => $paramName) {
            $builder->andWhere('r.'.$identifier.' = :'.$paramName);
            $builder->setParameter($paramName, $request->attributes->get($paramName));
        }

        $this->buildQueryForParent($builder, $request, $resource);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * Fetches a collection for the resource.
     *
     * @param Request $request
     * @param DomainResource $resource
     * @return array
     */
    protected function fetchCollection(Request $request, DomainResource $resource)
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
     * @param DomainResource $resource
     */
    private function buildQueryForParent(QueryBuilder $builder, Request $request, DomainResource $resource)
    {
        $parentAlias = 'r';
        $index = 1;
        $parent = $resource->getParent();
        while ($parent) {
            $alias = 'p'.$index;
            $builder->addSelect($alias)->innerJoin($parentAlias.'.'.$resource->getParentPath(), $alias);

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