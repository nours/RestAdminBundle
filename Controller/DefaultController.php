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
     * @param \Nours\RestAdminBundle\Domain\Resource $_resource
     * @return ArrayCollection
     */
    public function indexAction(Resource $_resource)
    {
        $data = $this->getDoctrine()->getRepository($_resource->getClass())->findAll();

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
     * @param \Nours\RestAdminBundle\Domain\Resource $_resource
     * @param Action $_action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction(Request $request, Resource $_resource, Action $_action)
    {
        $data = $this->createData($request);

        $form = $this->createResourceForm($data, $_resource, $_action);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->persist($data);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $_resource->getFullName().'.create');

            return $this->redirectToRoute($_resource->getRouteName('index'), $_resource->getRouteParams($data));
        }

        return $form;
    }

    /**
     * Create action
     *
     * @param Request $request
     * @param Resource $_resource
     * @param Action $_action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Resource $_resource, Action $_action, $data)
    {
        $form = $this->createResourceForm($data, $_resource, $_action);

        if ($request->getMethod() == $form->getConfig()->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                $this->addFlash('success', $_resource->getFullName().'.edit');

                return $this->redirectToRoute($_resource->getRouteName('index'));
            }
        }

        return $form;
    }

    /**
     * Create action
     *
     * @param Request $request
     * @param Resource $_resource
     * @param Action $_action
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, Resource $_resource, Action $_action, $data)
    {
        $form = $this->createResourceForm($data, $_resource, $_action);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->remove($data);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', $_resource->getFullName().'.delete');

            return $this->redirectToRoute($_resource->getRouteName('index'));
        }

        return $form;
    }
}