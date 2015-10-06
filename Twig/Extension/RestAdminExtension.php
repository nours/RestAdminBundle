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

    public function __construct(RequestStack $requestStack, AdminManager $adminManager, AdminHelper $helper)
    {
        $this->requestStack = $requestStack;
        $this->adminManager = $adminManager;
        $this->helper       = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('rest_action', array($this, 'createControllerReference'))
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
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    private function getCurrentResource()
    {
        $request = $this->getRequest();

        if (empty($request)) {
            throw new \RuntimeException("Cannot access current request from request stack");
        }

        return $request->attributes->get('resource');
    }

    /**
     * @return Request
     */
    private function getRequest()
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rest_admin';
    }
}