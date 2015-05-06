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
 * Class can either be passed as default or as class argument.
 *
 * @Annotation
 * @Target("CLASS")
 */
class Resource
{
    /**
     * The resource class name
     *
     * @var string
     */
    public $class;

    /**
     * The resource config array
     *
     * @var array
     */
    public $config;

    /**
     * Controller service id
     *
     * @var string
     */
    public $service;


    public function __construct(array $values)
    {
        if (isset($values['class'])) {
            $this->class = $values['class'];
        } elseif (isset($values['value'])) {
            $this->class = $values['value'];
        } else {
            throw new \InvalidArgumentException("Missing resource class");
        }

        unset($values['class']);
        unset($values['value']);

        if (isset($values['service'])) {
            $this->service = $values['service'];
            unset($values['service']);
        }

        $this->config = $values;
    }
}
