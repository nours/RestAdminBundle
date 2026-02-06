<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\DataFixtures;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\Common\DataFixtures\Purger\ORMPurgerInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SqlitePurger
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class SqlitePurger implements ORMPurgerInterface
{
    private ORMPurger $purger;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ?EntityManagerInterface $entityManager = null,
        $excluded = []
    ) {
        $this->entityManager = $entityManager;
        $this->purger = new ORMPurger($entityManager, $excluded);
    }

    /**
     * Purge the data from the database for the given EntityManager.
     *
     * @return void
     */
    function purge(): void
    {
        $this->purger->purge();

        $connection = $this->entityManager->getConnection();

        if ($connection->executeQuery("SELECT name FROM sqlite_master WHERE type='table' AND name='sqlite_sequence'")->fetchOne()) {
            $connection->executeStatement('DELETE FROM sqlite_sequence');
        }
    }

    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->purger->setEntityManager($em);
        $this->entityManager = $em;
    }
}
