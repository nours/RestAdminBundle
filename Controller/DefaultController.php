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
     * Create action
     *
     * @param Request $request
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, Resource $resource, Action $action)
    {
        $data = $this->createData($request);

        $form = $this->createResourceForm($data, $resource, $action);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $resource->getFullName().'.create');

            return $this->redirectToRoute($resource->getRouteName('index'), $resource->getRouteParams($data));
        }

        return $form;
    }

    /**
     * Create action
     *
     * @param Request $request
     * @param Resource $resource
     * @param Action $action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Resource $resource, Action $action, $data)
    {
        $form = $this->createResourceForm($data, $resource, $action);

        if ($request->getMethod() == $form->getConfig()->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', $resource->getFullName().'.edit');

                return $this->redirectToRoute($resource->getRouteName('index'));
            }
        }

        return $form;
    }

    /**
     * Create action
     *
     * @param Request $request
     * @param Resource $resource
     * @param Action $action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Resource $resource, Action $action, $data)
    {
        $form = $this->createResourceForm($data, $resource, $action);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->remove($data);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $resource->getFullName().'.delete');

            return $this->redirectToRoute($resource->getRouteName('index'));
        }

        return $form;
    }
}