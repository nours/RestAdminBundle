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
 * Class ResourceRoutingTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceRoutingTest extends AdminTestCase
{
    public function testGetBaseInstanceRouteParams()
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
         * First level (Contract) : route params
         */
        $this->assertEquals(array(
        ), $resourceContract->getBaseRouteParams($contract));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContract->getInstanceRouteParams($contract));

        /*
         * Second level (Contract / Order) : single (one per contract)
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrder->getBaseRouteParams($contract));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrder->getBaseRouteParams($order));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrder->getInstanceRouteParams($order));

        /*
         * Second level (Contract / Invoice) : multiple
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractInvoice->getBaseRouteParams($invoice));
        $this->assertEquals(array(
            'contract' => 1,
            'invoice'  => 1
        ), $resourceContractInvoice->getInstanceRouteParams($invoice));

        /*
         * Third level (Contract / Order / Invoice) : both are single
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderInvoice->getBaseRouteParams($order));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderInvoice->getBaseRouteParams($invoice));
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderInvoice->getInstanceRouteParams($invoice));

        /*
         * Third level (Contract / Order / Transaction) : single + multiple
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderTransaction->getBaseRouteParams($order));
        $this->assertEquals(array(
            'contract' => 1,
            'transaction' => 1
        ), $resourceContractOrderTransaction->getInstanceRouteParams($transaction));

        /*
         * Third level (Contract / Invoice / Transaction) : multiple + multiple
         */
        $this->assertEquals(array(
            'contract' => 1,
            'invoice'  => 1
        ), $resourceContractInvoiceTransaction->getBaseRouteParams($invoice));
        $this->assertEquals(array(
            'contract' => 1,
            'invoice'  => 1,
            'transaction' => 1
        ), $resourceContractInvoiceTransaction->getInstanceRouteParams($transaction));

        /*
         * Fourth level (Contract / Order / Invoice / Transaction) : single + single + multiple
         */
        $this->assertEquals(array(
            'contract' => 1
        ), $resourceContractOrderInvoiceTransaction->getBaseRouteParams($invoice));
        $this->assertEquals(array(
            'contract' => 1,
            'transaction' => 1
        ), $resourceContractOrderInvoiceTransaction->getInstanceRouteParams($transaction));
    }


    public function testGetBaseInstanceUriPath()
    {
        $this->loadFixtures();

        $resourceContract = $this->getAdminManager()->getResource('contract');
        $resourceContractOrder = $this->getAdminManager()->getResource('contract.order');
        $resourceContractInvoice = $this->getAdminManager()->getResource('contract.invoice');
        $resourceContractOrderInvoice = $this->getAdminManager()->getResource('contract.order.invoice');
        $resourceContractOrderTransaction = $this->getAdminManager()->getResource('contract.order.transaction');
        $resourceContractInvoiceTransaction = $this->getAdminManager()->getResource('contract.invoice.transaction');
        $resourceContractOrderInvoiceTransaction = $this->getAdminManager()->getResource('contract.order.invoice.transaction');

        $this->assertEquals('contracts', $resourceContract->getBaseUriPath());
        $this->assertEquals('contracts/{contract}', $resourceContract->getInstanceUriPath());

        $this->assertEquals('contracts/{contract}/order', $resourceContractOrder->getBaseUriPath());
        $this->assertEquals('contracts/{contract}/order', $resourceContractOrder->getInstanceUriPath());

        $this->assertEquals('contracts/{contract}/invoices', $resourceContractInvoice->getBaseUriPath());
        $this->assertEquals('contracts/{contract}/invoices/{invoice}', $resourceContractInvoice->getInstanceUriPath());

        $this->assertEquals('contracts/{contract}/order/invoice', $resourceContractOrderInvoice->getBaseUriPath());
        $this->assertEquals('contracts/{contract}/order/invoice', $resourceContractOrderInvoice->getInstanceUriPath());

        $this->assertEquals('contracts/{contract}/order/transactions', $resourceContractOrderTransaction->getBaseUriPath());
        $this->assertEquals('contracts/{contract}/order/transactions/{transaction}', $resourceContractOrderTransaction->getInstanceUriPath());

        $this->assertEquals('contracts/{contract}/invoices/{invoice}/transactions', $resourceContractInvoiceTransaction->getBaseUriPath());
        $this->assertEquals('contracts/{contract}/invoices/{invoice}/transactions/{transaction}', $resourceContractInvoiceTransaction->getInstanceUriPath());

        $this->assertEquals('contracts/{contract}/order/invoice/transactions', $resourceContractOrderInvoiceTransaction->getBaseUriPath());
        $this->assertEquals('contracts/{contract}/order/invoice/transactions/{transaction}', $resourceContractOrderInvoiceTransaction->getInstanceUriPath());
    }

    /**
     */
    public function testGetPrototypeRouteParams()
    {
        $resourceComposite = $this->getAdminManager()->getResource('composite');
        $resourceCompositeChild = $this->getAdminManager()->getResource('composite.composite_child');

        $this->assertEquals(array(
            'composite_id' => '__composite_id__',
            'composite_name' => '__composite_name__'
        ), $resourceComposite->getPrototypeRouteParams(true));
        $this->assertEquals(array(
            'composite_child_id' => '__composite_child_id__',
            'composite_child_name' => '__composite_child_name__',
            'composite_id' => '__composite_id__',
            'composite_name' => '__composite_name__'
        ), $resourceCompositeChild->getPrototypeRouteParams(true));
    }

    /**
     */
    public function testGetPrototypeParamsMapping()
    {
        $resourceComposite = $this->getAdminManager()->getResource('composite');
        $resourceCompositeChild = $this->getAdminManager()->getResource('composite.composite_child');

        $this->assertEquals(array(
            '__composite_id__'   => 'id',
            '__composite_name__' => 'name'
        ), $resourceComposite->getPrototypeParamsMapping(true));
        $this->assertEquals(array(
            '__composite_child_id__'   => 'id',
            '__composite_child_name__' => 'name',
            '__composite_id__'     => 'parent.id',
            '__composite_name__'   => 'parent.name'
        ), $resourceCompositeChild->getPrototypeParamsMapping(true));
    }
}