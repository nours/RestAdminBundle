<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Transaction
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @ORM\Entity()
 * @ORM\Table(name="`transaction`")
 */
class Transaction
{
    /**
     * @var integer
     *
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Invoice
     *
     * @ORM\OneToOne(targetEntity="Invoice")
     */
    private $invoice;

    /**
     * @var Order
     *
     * @ORM\OneToOne(targetEntity="Order")
     */
    private $order;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Invoice
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * @param Invoice $invoice
     */
    public function setInvoice($invoice)
    {
        $this->invoice = $invoice;
        $this->invoice->getTransactions()->add($this);
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param Order $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
        $this->order->getTransactions()->add($this);
    }
}