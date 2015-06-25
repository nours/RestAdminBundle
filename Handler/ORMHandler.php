<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Handler;
use Doctrine\ORM\EntityManager;


/**
 * Handler for ORM entities, for creation, update and delete actions.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ORMHandler
{
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handleCreate($data)
    {
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    public function handleUpdate()
    {
        $this->entityManager->flush();
    }

    public function handleDelete($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }

    public function handleBulkDelete($data)
    {
        foreach ($data as $object) {
            $this->entityManager->remove($object);
        }
        $this->entityManager->flush();
    }
}