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
 * @Target({"CLASS", "METHOD"})
 */
class Action
{
    public $name;
    public $options = array();

    public function __construct(array $values)
    {
        if (!isset($values['value'])) {
            throw new \InvalidArgumentException("Missing action name");
        }

        $this->name = $values['value'];

        unset($values['value']);
        $this->options = $values;
    }
}
