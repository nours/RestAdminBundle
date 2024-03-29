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
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class SqlitePurger
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class SqlitePurger extends ORMPurger
{
    public function __construct(EntityManagerInterface $em = null, $excluded = [])
    {
        parent::__construct($em, $excluded);
    }

    /**
     * Purge the data from the database for the given EntityManager.
     *
     * @return void
     */
    function purge()
    {
        parent::purge();

        $connection = $this->getObjectManager()->getConnection();

        if ($connection->executeQuery("SELECT name FROM sqlite_master WHERE type='table' AND name='sqlite_sequence'")->fetchOne()) {
            $this->getObjectManager()->getConnection()->executeStatement('DELETE FROM sqlite_sequence');
        }
    }
}