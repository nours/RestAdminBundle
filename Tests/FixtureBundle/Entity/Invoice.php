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
 * Class Invoice
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
#[ORM\Entity]
class Invoice
{
    /**
     * @var integer
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private $id;

    /**
     * @var Contract
     */
    #[ORM\ManyToOne(targetEntity: Contract::class, inversedBy: 'invoices')]
    private $contract;

    /**
     * @var Order
     */
    #[ORM\OneToOne(mappedBy: 'invoice', targetEntity: Order::class)]
    private $order;

    /**
     * @var Collection|Transaction[]
     */
    #[ORM\OneToMany(mappedBy: 'invoice', targetEntity: Transaction::class)]
    private $transactions;

    /**
     * Invoice constructor.
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
    }

    /**
     * @return Collection|Transaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }
}