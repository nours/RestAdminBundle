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
 * @Target("METHOD")
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Factory
{
    /**
     * @var string
     */
    public $action;

    /**
     * @var int
     */
    public $priority;

    public function __construct(array $values)
    {
        $this->action = isset($values['value']) ? $values['value'] : null;
    }
}