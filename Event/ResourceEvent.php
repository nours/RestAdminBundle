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

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * ResourceEvent, having knowledge of a resource and an action (optional)
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceEvent extends Event
{
    /**
     * @var DomainResource
     */
    private $resource;

    /**
     * @var Action
     */
    private $action;

    /**
     * @param DomainResource $resource
     * @param Action|null $action
     */
    public function __construct(DomainResource $resource, Action $action = null)
    {
        $this->resource = $resource;
        $this->action   = $action;
    }

    /**
     * @return DomainResource
     */
    public function getResource(): DomainResource
    {
        return $this->resource;
    }

    /**
     * @return Action
     */
    public function getAction(): Action
    {
        return $this->action;
    }
}