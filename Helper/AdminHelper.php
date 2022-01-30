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
use RuntimeException;
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
    public function createControllerReference($action, array $attributes = []): ControllerReference
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
     * @param int $referenceType
     *
     * @return string
     */
    public function generateUrl(
        $action = null,
        $data = null,
        array $routeParams = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $action = $this->getAction($action ?: $this->getCurrentAction());

        if (!is_array($routeParams)) {
            trigger_error("Using reference type parameter is deprecated, and is replaced by route params", E_USER_DEPRECATED);
        }

        // The action guesses its route params from data
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
    public function generateFormAction(
        $action = null,
        $data = null,
        array $routeParams = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $action = $this->getAction($action ?: $this->getCurrentAction());

        // The action guesses its route params from data
        $routeParams = array_merge($routeParams, $action->getRouteParams($data));

        return $this->urlGenerator->generate($action->getFormActionRouteName(), $routeParams, $referenceType);
    }

    /**
     * Action from string
     *
     * @param Action|string $name
     * @return Action
     */
    public function getAction($name): Action
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
            throw new RuntimeException("No current resource to get action '$name' from");
        }

        return $resource->getAction($name);
    }

    /**
     * @return DomainResource|null
     */
    public function getCurrentResource(): ?DomainResource
    {
        if ($request = $this->getRequest()) {
            return $request->attributes->get('resource');
        }

        return null;
    }

    /**
     * @return Action|null
     */
    public function getCurrentAction(): ?Action
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

        // Otherwise look for data, and retrieve its parent
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
    public function getCurrentRouteParams(): array
    {
        if ($action = $this->getCurrentAction()) {
            if ($data = $this->getResourceInstance()) {
                return $action->getRouteParams($data);
            } elseif ($parent = $this->getResourceParent()) {
                return $action->getRouteParams($parent);
            }
        }

        return [];
    }

    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param $name
     * @return DomainResource
     */
    public function getResource($name): DomainResource
    {
        return $this->adminManager->getResource($name);
    }
}