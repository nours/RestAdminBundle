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
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\EventDispatcher\Event;

/**
 * ResourceEvent, having knowledge of a resource and an action (optional)
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceEvent extends Event
{
    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $resource;

    /**
     * @var Action
     */
    private $action;

    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     */
    public function __construct(Resource $resource, Action $action = null)
    {
        $this->resource = $resource;
        $this->action   = $action;
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }
}