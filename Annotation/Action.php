<?php

namespace Nours\RestAdminBundle\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 */
class Action
{
    public $name;
    public $options = array();
}
