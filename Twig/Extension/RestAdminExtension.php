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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class RestAdminExtension
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RestAdminExtension extends AbstractExtension
{
    private $requestStack;
    private $adminManager;
    private $helper;
    private $actionTemplate;

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
    public function getFunctions()
    {
        return array(
            new TwigFunction('rest_action', array($this, 'createControllerReference')),
            new TwigFunction('rest_action_path', array($this, 'getActionPath')),
            new TwigFunction('rest_action_url', array($this, 'getActionUrl')),
            new TwigFunction('rest_action_controller', array($this, 'createControllerReference')),
            new TwigFunction('rest_action_link', array($this, 'renderActionLink'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
            new TwigFunction('rest_action_link_prototype', array($this, 'renderActionPrototype'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            ))
        );
    }

    /**
     * @param Action|string $action
     * @param mixed $data
     * @param array $params
     *
     * @return string
     */
    public function getActionPath($action, $data = null, array $params = []): string
    {
        $action = $this->helper->getAction($action);

        return $this->helper->generateUrl($action, $data, $params);
    }

    /**
     * @param Action|string $action
     * @param mixed $data
     * @param array $params
     *
     * @return string
     */
    public function getActionUrl($action, $data = null, array $params = []): string
    {
        $action = $this->helper->getAction($action);

        return $this->helper->generateUrl($action, $data, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }

    /**
     * @param Action|string $action
     * @param array $attributes
     * @return ControllerReference
     */
    public function createControllerReference($action, array $attributes = array()): ControllerReference
    {
        return $this->helper->createControllerReference($action, $attributes);
    }

    /**
     * @param Environment $environment
     * @param string|Action $action
     * @param $data
     * @param array $options
     * @return string
     */
    public function renderActionLink(Environment $environment, $action, $data = null, array $options = array())
    {
        $action = $this->helper->getAction($action);

        $routeParams = $options['routeParams'] ?? array();

        if ($data) {
            $routeParams = array_merge($action->getRouteParams($data), $routeParams);
        } elseif (!$routeParams) {
            $routeParams = $this->helper->getCurrentRouteParams();
        }
        $options['routeParams'] = $routeParams;

        $context = $this->makeActionContext($action, $options);
        $template = isset($options['template']) ? $options['template'] : $this->getTemplate($action);

        return $environment->render($template, $context);
    }

    /**
     * @param Environment $environment
     * @param string|Action $action
     * @param array $options
     * @return string
     */
    public function renderActionPrototype(Environment $environment, $action, array $options = array()): string
    {
        $action = $this->helper->getAction($action);

        if (!isset($options['routeParams'])) {
            $options['routeParams'] = array();
        }
        $options['routeParams'] = array_merge($action->getPrototypeRouteParams(), $options['routeParams']);

        $context = $this->makeActionContext($action, $options);
        $template = $options['template'] ?? $this->getTemplate($action);

        return $environment->render($template, $context);
    }


    private function getTemplate(Action $action)
    {
        return $action->getConfig('action_template', $this->actionTemplate);
    }

    /**
     * @param Action $action
     * @param array $options
     * @return array
     */
    private function makeActionContext(Action $action, array $options): array
    {
        return array_merge(array(
            'label'       => $action->getConfig('label', $action->getName()),
            'route'       => $action->getRouteName(),
            'routeParams' => array(),
            'attr'        => array(),
            'resource'    => $action->getResource(),
            'action'      => $action
        ), $options);
    }
}