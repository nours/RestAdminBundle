<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Domain;

use Nours\RestAdminBundle\Tests\AdminTestCase;

/**
 * Class ActionRoutingTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionRoutingTest extends AdminTestCase
{
    public function testGetRouteParams()
    {
        $this->loadFixtures();

        $resourceContract = $this->getAdminManager()->getResource('contract');
        $resourceContractOrder = $this->getAdminManager()->getResource('contract.order');
        $resourceContractInvoice = $this->getAdminManager()->getResource('contract.invoice');
        $resourceContractOrderInvoice = $this->getAdminManager()->getResource('contract.order.invoice');
        $resourceContractOrderTransaction = $this->getAdminManager()->getResource('contract.order.transaction');
        $resourceContractInvoiceTransaction = $this->getAdminManager()->getResource('contract.invoice.transaction');
        $resourceContractOrderInvoiceTransaction = $this->getAdminManager()->getResource('contract.order.invoice.transaction');

        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
        $order    = $this->getEntityManager()->find('FixtureBundle:Order', 1);
        $invoice  = $this->getEntityManager()->find('FixtureBundle:Invoice', 1);
        $transaction = $this->getEntityManager()->find('FixtureBundle:Transaction', 1);

        /*
         * First level (Contract)
         */
        $this->assertEquals(array(), $resourceContract->getAction('index')->getRouteParams($contract));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContract->getAction('get')->getRouteParams($contract));

        /*
         * Second level (Contract / Order) : single (one per contract)
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrder->getAction('get')->getRouteParams($contract));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrder->getAction('get')->getRouteParams($order));

        /*
         * Second level (Contract / Invoice) : multiple
         */
        $this->assertEquals(array(
            'contract' => 1,
            'invoice'  => 1
        ), $resourceContractInvoice->getAction('get')->getRouteParams($invoice));

        /*
         * Third level (Contract / Order / Transaction) : single + multiple
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderTransaction->getAction('index')->getRouteParams($order));
        $this->assertEquals(array(
            'contract' => 1,
            'transaction' => 1
        ), $resourceContractOrderTransaction->getAction('get')->getRouteParams($transaction));

        /*
         * Third level (Contract / Invoice / Transaction) : multiple + multiple
         */
        $this->assertEquals(array(
            'contract' => 1,
            'invoice'  => 1
        ), $resourceContractInvoiceTransaction->getAction('index')->getRouteParams($invoice));
        $this->assertEquals(array(
            'contract' => 1,
            'invoice'  => 1,
            'transaction' => 1
        ), $resourceContractInvoiceTransaction->getAction('get')->getRouteParams($transaction));

        /*
         * Third level (Contract / Order / Invoice) : single + single
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderInvoice->getAction('get')->getRouteParams($invoice));

        /*
         * Fourth level (Contract / Order / Invoice / Transaction) : single + single + multiple
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderInvoiceTransaction->getAction('index')->getRouteParams($invoice));
        $this->assertEquals(array(
            'contract' => 1,
            'transaction' => 1
        ), $resourceContractOrderInvoiceTransaction->getAction('get')->getRouteParams($transaction));
    }


    public function testGetRouteParamsThrowsIfResourceDoNotMatch()
    {
        $this->loadFixtures();

        $resourceContractOrderTransaction = $this->getAdminManager()->getResource('contract.order.transaction');

        $this->expectException(\InvalidArgumentException::class);

        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);

        $resourceContractOrderTransaction->getAction('get')->getRouteParams($contract);
    }
}