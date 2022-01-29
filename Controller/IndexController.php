<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Nours\RestAdminBundle\Domain\DomainResource;

/**
 * Class IndexController
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class IndexController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DomainResource $resource)
    {
        $data = $this->entityManager->getRepository($resource->getClass())->findAll();

        return new ArrayCollection($data);
    }
}