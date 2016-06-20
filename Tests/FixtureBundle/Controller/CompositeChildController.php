<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Controller;

use Nours\RestAdminBundle\Annotation as Rest;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Foo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CompositeChildController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\CompositeChild",
 *  identifier = { "id", "name" },
 *  parent = "composite",
 *  parent_association = "parent",
 *  slug = "children"
 * )
 *
 * @Rest\Action("index", template="composite/child/index.html.twig")
 * @Rest\Action("get", template="composite/child/get.html.twig")
 */
class CompositeChildController
{

}