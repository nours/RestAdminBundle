<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Persistence\ObjectManager;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Contract;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Invoice;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Order;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Transaction;

/**
 * Class LoadContracts
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class LoadContracts extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $contract = new Contract(); // 1
        $manager->persist($contract);

        $invoice = new Invoice($contract); // 1
        $manager->persist($invoice);

        $order = new Order($contract); // 1
        $order->setInvoice($invoice);
        $manager->persist($order);

        $transaction = new Transaction(); // 1
        $transaction->setInvoice($invoice);
        $transaction->setOrder($order);
        $manager->persist($transaction);

        $manager->flush();
    }
}