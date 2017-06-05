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
    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $disabled = false;

    /**
     * @var array
     */
    public $options = array();

    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
            unset($values['value']);
        }

        // Disabled flag must not pass to options array
        // and is instead stored in disabled param in order to process it
        if (isset($values['disabled'])) {
            $this->disabled = $values['disabled'];
            unset($values['disabled']);
        }

        $this->options = $values;
    }
}
