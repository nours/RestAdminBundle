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
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\PostExtension;

/**
 * Class PostExtensionController
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\PostExtension",
 *  parent="post",
 *  name="extension",
 *  single=true,
 *  form="Nours\RestAdminBundle\Tests\FixtureBundle\Form\PostExtensionType"
 * )
 *
 * @Rest\Action("get", template="extension/get.html.twig")
 * @Rest\Action("create", template="extension/create.html.twig")
 * @Rest\Action("edit")
 * @Rest\Action("delete")
 */
class PostExtensionController
{
    /**
     * @Rest\Factory("create")
     *
     * @param Post $parent
     *
     * @return PostExtension
     */
    public function factory(Post $parent)
    {
        $extension = new PostExtension($parent);
        $extension->setPost($parent);
        $parent->setExtension($extension);

        return $extension;
    }
}