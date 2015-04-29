<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Nours\RestAdminBundle\Persistance\Repository as BaseRepository;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Repository
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineRepository implements BaseRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    public function find(Resource $resource, Request $request)
    {
        $repository = $this->manager->getRepository($resource->getClass());

        return $repository->findOneBy(array(
            $resource->getIdentifier() => $request->attributes->get($resource->getName())
        ));
    }

    public function supports(Resource $resource)
    {
        return $this->manager->getMetadataFactory()->hasMetadataFor($resource->getClass());
    }
}