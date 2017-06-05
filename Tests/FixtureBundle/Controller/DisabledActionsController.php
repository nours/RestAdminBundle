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

/**
 * Class DisabledActionsController
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Foo",
 *  name = "disabled_actions"
 * )
 *
 * @Rest\Action("index", disabled=true)
 * @Rest\Action("get", disabled=true)
 */
class DisabledActionsController
{

}