<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\Doctrine;

use Nours\RestAdminBundle\ParamFetcher\DoctrineParamFetcher;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DoctrineParamFetcherBisTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineParamFetcherBisTest extends AdminTestCase
{
    /**
     * @var DoctrineParamFetcher
     */
    private $fetcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures();

        $this->fetcher = new DoctrineParamFetcher($this->getEntityManager());
    }


    /**
     */
    public function testFetchContract()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);

        $resource = $this->getAdminManager()->getResource('contract');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($contract, $request->attributes->get('data'));
    }

    /**
     */
    public function testFetchContractOrder()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
        $order    = $this->getEntityManager()->find('FixtureBundle:Order', 1);

        $resource = $this->getAdminManager()->getResource('contract.order');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($order, $request->attributes->get('data'));
        $this->assertSame($contract, $request->attributes->get('parent'));
    }

    /**
     */
    public function testFetchContractInvoice()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
//        $order    = $this->getEntityManager()->find('FixtureBundle:Order', 1);
        $invoice  = $this->getEntityManager()->find('FixtureBundle:Invoice', 1);
//        $transaction = $this->getEntityManager()->find('FixtureBundle:Transaction', 1);

        $resource = $this->getAdminManager()->getResource('contract.invoice');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId(),
            'invoice' => $invoice->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($invoice, $request->attributes->get('data'));
        $this->assertSame($contract, $request->attributes->get('parent'));
    }

    /**
     */
    public function testFetchContractOrderInvoice()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
        $order    = $this->getEntityManager()->find('FixtureBundle:Order', 1);
        $invoice  = $this->getEntityManager()->find('FixtureBundle:Invoice', 1);

        $resource = $this->getAdminManager()->getResource('contract.order.invoice');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($invoice, $request->attributes->get('data'));
        $this->assertSame($order, $request->attributes->get('parent'));
    }

    /**
     */
    public function testFetchContractOrderTransaction()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
        $order    = $this->getEntityManager()->find('FixtureBundle:Order', 1);
        $transaction = $this->getEntityManager()->find('FixtureBundle:Transaction', 1);

        $resource = $this->getAdminManager()->getResource('contract.order.transaction');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId(),
            'transaction' => $transaction->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($transaction, $request->attributes->get('data'));
        $this->assertSame($order, $request->attributes->get('parent'));
    }

    /**
     */
    public function testFetchContractInvoiceTransaction()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
        $invoice  = $this->getEntityManager()->find('FixtureBundle:Invoice', 1);
        $transaction = $this->getEntityManager()->find('FixtureBundle:Transaction', 1);

        $resource = $this->getAdminManager()->getResource('contract.invoice.transaction');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId(),
            'invoice' => $invoice->getId(),
            'transaction' => $transaction->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($transaction, $request->attributes->get('data'));
        $this->assertSame($invoice, $request->attributes->get('parent'));
    }

    /**
     */
    public function testFetchContractOrderInvoiceTransaction()
    {
        $contract = $this->getEntityManager()->find('FixtureBundle:Contract', 1);
        $order    = $this->getEntityManager()->find('FixtureBundle:Order', 1);
        $invoice  = $this->getEntityManager()->find('FixtureBundle:Invoice', 1);
        $transaction = $this->getEntityManager()->find('FixtureBundle:Transaction', 1);

        $resource = $this->getAdminManager()->getResource('contract.order.invoice.transaction');

        $request = new Request();
        $request->attributes->add(array(
            'resource' => $resource,
            'action' => $resource->getAction('get'),
            'contract' => $contract->getId(),
            'transaction' => $transaction->getId()
        ));

        $this->fetcher->fetch($request);

        $this->assertSame($transaction, $request->attributes->get('data'));
        $this->assertSame($invoice, $request->attributes->get('parent'));
    }
}