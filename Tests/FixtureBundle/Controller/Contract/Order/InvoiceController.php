<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Controller\Contract\Order;

use Nours\RestAdminBundle\Annotation as Rest;

/**
 * Class InvoiceController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\DomainResource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Invoice",
 *  parent = "contract.order",
 *  single = true
 * )
 */
class InvoiceController
{

}