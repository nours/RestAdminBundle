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
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return ArrayCollection
     */
    public function indexAction(Request $request, Resource $resource)
    {
        if ($tableName = $resource->getConfig('table')) {
            $table = $this->get('nours_table.factory')->createTable($tableName, array(
                'resource' => $resource
            ));
            return $table->handle($request)->createView();
        }

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
     * @param Action $action
     * @param mixed $data
     * @return ArrayCollection
     */
    public function formAction(Request $request, Action $action, $data = null)
    {
        // Initialize data if not found from routing
        $data = $data ?: $this->createData($request);

        // Create form
        $form = $this->createResourceForm($data, $action);

        if ($request->getMethod() == $form->getConfig()->getMethod()) {
            // Handle request only if method matches
            $form->handleRequest($request);

            if ($form->isValid()) {
                return $this->handleSuccess($data, $request, $form, $action);
            }
        }

        return $form;
    }
}