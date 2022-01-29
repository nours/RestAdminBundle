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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Class Controller
 *
 * @deprecated TO DELETE AND REPLACE BY AbstractController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Controller extends AbstractController
{
    public static function getSubscribedServices()
    {
        return array_merge(parent::getSubscribedServices(), [
//            'rest_admin.action_form_factory',
//            'rest_admin.form_success_handler'
        ]);
    }

    /**
     * @param $data
     * @param Action $action
     * @return \Symfony\Component\Form\Form
     */
    protected function createResourceForm($data, Action $action)
    {
        return $this->container->get('rest_admin.action_form_factory')->createForm($data, $action);
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
        return $this->container->get('rest_admin.form_success_handler')->handle($data, $request, $form, $action);
    }
}