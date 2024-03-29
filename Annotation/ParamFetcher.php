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


/**
 * @Annotation
 * @Target("METHOD")
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ParamFetcher
{
    public $action;

    public function __construct(array $values)
    {
        $this->action = $values['value'] ?? null;
    }
}