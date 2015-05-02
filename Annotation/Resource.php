<?php

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
    public $templates;
}
