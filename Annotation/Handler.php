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
use InvalidArgumentException;

/**
 * @Annotation
 * @Target("METHOD")
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Handler
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
        if (!isset($values['value'])) {
            throw new InvalidArgumentException("Missing action name");
        }

        $this->action = $values['value'];

        $this->priority = $values['priority'] ?? null;
    }
}