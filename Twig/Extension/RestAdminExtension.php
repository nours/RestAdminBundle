<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Twig\Extension;

use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Helper\AdminHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/**
 * Class RestAdminExtension
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RestAdminExtension extends \Twig_Extension
{
    private $requestStack;
    private $adminManager;
    private $helper;
    private $actionTemplate;

    /**
     * @var \Twig_Environment
     */
    private $environment;

    public function __construct(RequestStack $requestStack, AdminManager $adminManager, AdminHelper $helper, $actionTemplate)
    {
        $this->requestStack   = $requestStack;
        $this->adminManager   = $adminManager;
        $this->helper         = $helper;
        $this->actionTemplate = $actionTemplate;
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('rest_action', array($this, 'createControllerReference')),
            new \Twig_SimpleFunction('rest_action_controller', array($this, 'createControllerReference')),
            new \Twig_SimpleFunction('rest_action_link', array($this, 'renderActionLink')),
            new \Twig_SimpleFunction('rest_action_link_prototype', array($this, 'renderActionPrototype'))
        );
    }

    /**
     * @param Action|string $action
     * @param array $attributes
     * @return ControllerReference
     */
    public function createControllerReference($action, array $attributes = array())
    {
        return $this->helper->createControllerReference($action, $attributes);
    }

    /**
     * @param string|Action $action
     * @param $data
     * @param array $options
     * @return string
     */
    public function renderActionLink($action, $data = null, array $options = array())
    {
        $action = $this->helper->getAction($action);

        if ($data) {
            $options['routeParams'] = $action->getRouteParams($data);
        } else {
            $options['routeParams'] = $this->helper->getCurrentRouteParams();
        }

        $context = $this->makeActionContext($action, $options);

        return $this->environment->render($this->actionTemplate, $context);
    }

    /**
     * @param string|Action $action
     * @param array $options
     * @return string
     */
    public function renderActionPrototype($action, array $options = array())
    {
        $action = $this->helper->getAction($action);

        $options['routeParams'] = $action->getPrototypeRouteParams();

        $context = $this->makeActionContext($action, $options);

        return $this->environment->render($this->actionTemplate, $context);
    }

    /**
     * @param Action $action
     * @param $routeParams
     * @param array $options
     * @return array
     */
    private function makeActionContext(Action $action, array $options)
    {
        return array_merge(array(
            'icon'        => $action->getConfig('icon'),
            'label'       => $action->getConfig('label', $action->getName()),
            'route'       => $action->getRouteName(),
            'routeParams' => array(),
            'attr'        => array(),
            'resource'    => $action->getResource(),
            'action'      => $action
        ), $options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rest_admin';
    }
}