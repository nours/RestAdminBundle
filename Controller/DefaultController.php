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

use Doctrine\Common\Collections\ArrayCollection;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DefaultController extends Controller
{
    /**
     * Index action
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return ArrayCollection
     */
    public function indexAction(Resource $resource)
    {
        $data = $this->getDoctrine()->getRepository($resource->getClass())->findAll();

        return new ArrayCollection($data);
    }

    /**
     * Get action
     *
     * @param mixed $data
     * @return ArrayCollection
     */
    public function getAction($data)
    {
        return $data;
    }

    /**
     * Form handling action
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @param mixed $data
     * @return ArrayCollection
     */
    public function formAction(Request $request, Resource $resource, Action $action, $data = null)
    {
        // Initialize data if not found from routing
        $data = $data ?: $this->createData($request);

        // Create form
        $form = $this->createResourceForm($data, $resource, $action);

        if ($request->getMethod() == $form->getConfig()->getMethod()) {
            // Handle request only if method matches
            $form->handleRequest($request);

            if ($form->isValid()) {
                return $this->handleSuccess($data, $request, $form, $resource, $action);
            }
        }

        return $form;
    }
}