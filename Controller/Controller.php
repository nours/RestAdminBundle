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
use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Form\FormInterface;
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
        return $this->get('rest_admin.data_factory')->create($request);
    }

    /**
     * @param $data
     * @param Action $action
     * @return \Symfony\Component\Form\Form
     */
    protected function createResourceForm($data, Action $action)
    {
        return $this->get('rest_admin.action_form_factory')->createForm($data, $action);
    }

    /**
     * @param mixed $data
     * @param Request $request
     * @param FormInterface $form
     * @param Action $action
     * @return \Symfony\Component\Form\Form
     */
    protected function handleSuccess($data, Request $request, FormInterface $form, Action $action)
    {
        return $this->get('rest_admin.form_success_handler')->handle($data, $request, $form, $action);
    }
}