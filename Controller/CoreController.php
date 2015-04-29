<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Controller;
use Nours\RestAdminBundle\Action\ActionFormFactory;
use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\Api\ApiEventDispatcher;
use Nours\RestAdminBundle\Api\ApiEvents;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class CoreController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CoreController
{
    private $manager;
    private $dispatcher;
    private $formFactory;

    /**
     * @param AdminManager $manager
     * @param ApiEventDispatcher $dispatcher
     * @param ActionFormFactory $formFactory
     */
    public function __construct(AdminManager $manager, ApiEventDispatcher $dispatcher, ActionFormFactory $formFactory)
    {
        $this->manager       = $manager;
        $this->dispatcher    = $dispatcher;
        $this->formFactory   = $formFactory;
    }

    /**
     * Index controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $event = $this->makeEvent($request);
        $this->dispatcher->dispatch(ApiEvents::EVENT_LOAD, $event);

        if ($response = $event->getResponse()) {
            return $response;
        }


        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_VIEW, $event->copy());

        return $event->getResponse();
    }

    /**
     * Get controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAction(Request $request)
    {
        $event = $this->makeEvent($request);
        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_GET, $event->copy());

        if ($response = $event->getResponse()) {
            return $response;
        }

        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_VIEW, $event->copy());

        return $event->getResponse();
    }

    /**
     * Create controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request)
    {
        $event = $this->makeEvent($request);
        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_CREATE, $event->copy());

        if ($response = $event->getResponse()) {
            return $response;
        }

        return $this->handleForm($request, $event);
    }

    /**
     * Edit controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $event = $this->makeEvent($request);
        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_GET, $event->copy());

        if ($response = $event->getResponse()) {
            return $response;
        }

        return $this->handleForm($request, $event);
    }

    /**
     * Delete controller action
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request)
    {
        $event = $this->makeEvent($request);
        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_GET, $event->copy());

        if ($response = $event->getResponse()) {
            return $response;
        }

        return $this->handleForm($request, $event);
    }

    /**
     * Basic form handling
     *
     * @param Request $request
     * @param ApiEvent $event
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleForm(Request $request, ApiEvent $event)
    {
        $form = $this->getForm($event);
        $event->setForm($form);

        if ($request->getMethod() === $form->getConfig()->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $event = $this->dispatcher->dispatch(ApiEvents::EVENT_SUCCESS, $event);
            } else {
                $event = $this->dispatcher->dispatch(ApiEvents::EVENT_ERROR, $event);
            }

            if ($response = $event->getResponse()) {
                return $response;
            }
        }

        $event = $this->dispatcher->dispatch(ApiEvents::EVENT_VIEW, $event->copy());

        return $event->getResponse();
    }

    /**
     * @param ApiEvent $event
     * @return \Symfony\Component\Form\FormInterface
     */
    private function getForm(ApiEvent $event)
    {
        return $this->formFactory->createForm($event->getResource(), $event->getAction(), $event->getModel());
    }

    /**
     * @param Request $request
     * @return ApiEvent
     */
    private function makeEvent(Request $request)
    {
        $resource = $this->manager->getResource($request->attributes->get('_resource'));
        $action = $resource->getAction($request->attributes->get('_action'));

        return new ApiEvent($request, $resource, $action);
    }

    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}