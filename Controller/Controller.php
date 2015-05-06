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

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Controller
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Controller extends BaseController
{
    /**
     * @param Request $request
     * @return mixed
     */
    protected function createData(Request $request)
    {
        return $this->get('rest_admin.resource_factory')->createResource($request);
    }

    /**
     * @param $data
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @return \Symfony\Component\Form\Form
     */
    protected function createResourceForm($data, Resource $resource, Action $action)
    {
        $formName = $action->getForm() ?: $resource->getForm();

        if (empty($formName)) {
            throw new \DomainException(sprintf(
                "Resource %s Action %s doesn't have any form",
                $resource->getFullName(), $action->getName()
            ));
        }

        return $this->get('rest_admin.action_form_factory')->createForm($data, $resource, $action, array(
//            'csrf_protection' => false
        ));
    }
}