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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Order
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @ORM\Entity()
 * @ORM\Table(name="`order`")
 */
class Order
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
     * @var Contract
     *
     * @ORM\OneToOne(targetEntity="Contract", inversedBy="order")
     */
    private $contract;

    /**
     * @var Invoice
     *
     * @ORM\OneToOne(targetEntity="Invoice", inversedBy="order")
     */
    private $invoice;

    /**
     * @var Collection|Transaction[]
     *
     * @ORM\OneToMany(targetEntity="Transaction", mappedBy="order")
     */
    private $transactions;

    /**
     * Order constructor.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
        $this->transactions = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Contract
     */
    public function getContract()
    {
        return $this->contract;
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
        $this->invoice->setOrder($this);
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}