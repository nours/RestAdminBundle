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
 * Class Contract
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @ORM\Entity()
 */
class Contract
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
     * @var Order
     *
     * @ORM\OneToOne(targetEntity="order", mappedBy="contract")
     */
    private $order;

    /**
     * @var Collection|Invoice[]
     *
     * @ORM\OneToMany(targetEntity="Invoice", mappedBy="contract")
     */
    private $invoices;

    /**
     * Contract constructor.
     */
    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return Collection|Invoice[]
     */
    public function getInvoices()
    {
        return $this->invoices;
    }
}