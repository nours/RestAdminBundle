<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Resource
{
    public $name;
    public $class;
    public $parent;
    public $identifier;
    public $form;
    public $slug;

    public $options;

    /**
     * Controller service id
     *
     * @var string
     */
    public $service;
}
