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
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ControllerEvent
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ControllerEvent extends Event
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Action
     */
    private $action;

    /**
     * @var \Nours\RestAdminBundle\Domain\Resource
     */
    private $resource;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     */
    public function __construct(Request $request, Resource $resource, Action $action)
    {
        $this->request = $request;
        $this->resource = $resource;
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;

        $this->stopPropagation();
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
    }

}