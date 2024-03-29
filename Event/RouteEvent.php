<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Event;

use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Domain\Action;

/**
 * Class RouteEvent
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RouteEvent extends ResourceEvent
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var array
     */
    public $defaults;

    /**
     * @var array
     */
    public $requirements;

    /**
     * @var array
     */
    public $options;

    /**
     * @var string|string[]
     */
    public $method;

    /**
     * @var string
     */
    public $host;

    /**
     * @var string|string[]
     */
    public $schemes = [];

    /**
     * @param DomainResource $resource
     * @param Action $action
     * @param $path
     * @param array $defaults
     * @param array $requirements
     * @param array $options
     * @param string|string[] $method
     */
    public function __construct(
        DomainResource $resource,
        Action $action,
        $path,
        array $defaults,
        array $requirements,
        array $options,
        $method
    ) {
        parent::__construct($resource, $action);

        $this->path     = $path;
        $this->defaults = $defaults;
        $this->requirements = $requirements;
        $this->options  = $options;
        $this->method   = $method;
    }
}