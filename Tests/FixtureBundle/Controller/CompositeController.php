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
 * Class CompositeController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Composite",
 *  identifier = { "id", "name" },
 *  form = "Nours\RestAdminBundle\Tests\FixtureBundle\Form\CompositeType"
 * )
 *
 * @Rest\Action("index", template="composite/index.html.twig")
 * @Rest\Action("get", template="composite/get.html.twig")
 * @Rest\Action("create", template="composite/create.html.twig")
 * @Rest\Action("edit", template="composite/edit.html.twig")
 * @Rest\Action("delete", template="composite/delete.html.twig")
 */
class CompositeController
{

}