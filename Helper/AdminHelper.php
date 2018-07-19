<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Helper;

use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Class AdminHelper
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminHelper
{
    private $requestStack;
    private $adminManager;
    private $urlGenerator;

    /**
     * @param RequestStack $requestStack
     * @param AdminManager $adminManager
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(RequestStack $requestStack, AdminManager $adminManager, UrlGeneratorInterface $urlGenerator)
    {
        $this->requestStack = $requestStack;
        $this->adminManager = $adminManager;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Action|string $action
     * @param array $attributes
     * @return ControllerReference
     */
    public function createControllerReference($action, array $attributes = array())
    {
        $request = $this->getRequest();

        $defaults = $request ? $request->attributes->all() : array();

        $action = $this->getAction($action);

        $attributes = array_merge($defaults, array(
            'resource'    => $action->getResource(),
            'action'      => $action
        ), $attributes);

        return new ControllerReference($action->getController(), $attributes);
    }

    /**
     * Generates url for an action.
     *
     * @param string|Action|null $action
     * @param mixed $data
     * @param array $routeParams
     * @param string $referenceType
     * @return string
     */
    public function generateUrl($action = null, $data = null, $routeParams = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $action = $this->getAction($action ?: $this->getCurrentAction());

        if (!is_array($routeParams)) {
            trigger_error("Using reference type parameter is deprecated, and is replaced by route params", E_USER_DEPRECATED);
        }

        // The action guesses it's route params from data
        $routeParams = array_merge($routeParams, $action->getRouteParams($data));

        return $this->urlGenerator->generate($action->getRouteName(), $routeParams, $referenceType);
    }

    /**
     * Generate the url for the form action's post
     *
     * @param null $action
     * @param null $data
     * @param array $routeParams
     * @param int $referenceType
     *
     * @return string
     */
    public function generateFormAction($action = null, $data = null, $routeParams = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $action = $this->getAction($action ?: $this->getCurrentAction());

        // The action guesses it's route params from data
        $routeParams = array_merge($routeParams, $action->getRouteParams($data));

        return $this->urlGenerator->generate($action->getFormActionRouteName(), $routeParams, $referenceType);
    }

    /**
     * Action from string
     *
     * @param Action|string $name
     * @return Action
     */
    public function getAction($name)
    {
        if ($name instanceof Action) {
            return $name;
        }

        if (strpos($name, ':') !== false) {
            // Fully qualified action name
            return $this->adminManager->getAction($name);
        }

        // Action relative to current resource
        if (!($resource = $this->getCurrentResource())) {
            throw new \RuntimeException("No current resource to get action '$name' from");
        }

        return $resource->getAction($name);
    }

    /**
     * @return DomainResource
     */
    public function getCurrentResource()
    {
        if ($request = $this->getRequest()) {
            return $request->attributes->get('resource');
        }

        return null;
    }

    /**
     * @return Action
     */
    public function getCurrentAction()
    {
        $request = $this->getRequest();
        return $request ? $request->attributes->get('action') : null;
    }

    /**
     * If the action has one resource instance, returns it.
     *
     * Collections are ignored (although they are set in data parameter).
     *
     * @return mixed
     */
    public function getResourceInstance()
    {
        $request = $this->getRequest();
        $data = $request ? $request->attributes->get('data') : null;

        if (!is_array($data)) {
            return $data;
        }

        return null;
    }

    /**
     * The data of the resource, if the action concerns one collection.
     *
     * @return mixed
     */
    public function getResourceCollection()
    {
        $data = $this->getRequest()->attributes->get('data');

        if (is_array($data)) {
            return $data;
        }

        return null;
    }

    /**
     * The parent resource
     *
     * @return mixed
     */
    public function getResourceParent()
    {
        // If parent is set in request, return it
        if ($parent = $this->getRequest()->attributes->get('parent')) {
            return $parent;
        }

        // Otherwise look for data, and retrieve it's parent
        if ($data = $this->getResourceInstance()) {
            return $this->getCurrentResource()->getParentObject($data);
        }

        return null;
    }

    /**
     * The current route parameters
     *
     * @return array
     */
    public function getCurrentRouteParams()
    {
        if ($action = $this->getCurrentAction()) {
            if ($data = $this->getResourceInstance()) {
                return $action->getRouteParams($data);
            } elseif ($parent = $this->getResourceParent()) {
                return $action->getRouteParams($parent);
            }
        }

        return array();
    }

    /**
     * @deprecated
     *
     * The route parameters for the current resource base
     *
     * @return array
     */
    public function getResourceBaseRouteParams()
    {
        if ($parent = $this->getResourceParent()) {
            return $this->getCurrentResource()->getBaseRouteParams($parent);
        }

        return array();
    }

    /**
     * The route parameters for some data instance
     *
     * @param mixed $data
     * @return array
     */
    public function getDataRouteParams($data)
    {
        return $this->getCurrentResource()->getInstanceRouteParams($data);
    }

    /**
     * The route parameters for a parent resource
     *
     * @param mixed $parent
     * @return array
     */
    public function getParentRouteParams($parent)
    {
        return $this->getCurrentResource()->getBaseRouteParams($parent);
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request;
    }

    /**
     * @param $name
     * @return DomainResource
     */
    public function getResource($name)
    {
        return $this->adminManager->getResource($name);
    }
}