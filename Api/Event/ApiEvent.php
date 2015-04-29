<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Api\Event;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RequestEvent
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ApiEvent extends Event
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
     * @var Response
     */
    private $response;

    /**
     * @var mixed
     */
    private $model;

    /**
     * @var FormInterface
     */
    private $form;

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
     * @return ApiEvent
     */
    public function copy()
    {
        $copy = new self($this->request, $this->resource, $this->action);
        if ($model = $this->getModel()) {
            $copy->setModel($model);
        }
        if ($form = $this->getForm()) {
            $copy->setForm($form);
        }
        return $copy;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
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

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->stopPropagation();
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param FormInterface $form
     */
    public function setForm(FormInterface $form)
    {
        $this->form = $form;
    }
}