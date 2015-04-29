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
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RestAdminExtension
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RestAdminExtension extends \Twig_Extension
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('rest_admin_title', array($this, 'getAdminTitle'))
        );
    }

    public function getAdminTitle()
    {
        list($resource, $action) = $this->getResourceAction();
        return implode('.', array($resource, 'title', $action));
    }

    protected function getResourceAction()
    {
        $request = $this->requestStack->getCurrentRequest();

        return array($request->attributes->get('_resource'), $request->attributes->get('_action'));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'rest_admin';
    }
}