<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Routing;

use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * UrlGenerator for actions
 *
 * @deprecated use Helper\AdminHelper instead
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class UrlGenerator
{
    private $generator;

    /**
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        trigger_error("UrlGenerator is deprecated and will be deleted. Please use AdminHelper", E_USER_DEPRECATED);

        $this->generator = $generator;
    }

    /**
     * @param Action $action
     * @param array $params
     * @param integer $referenceType
     * @return string
     */
    public function generate(Action $action, array $params = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $resource = $action->getResource();
        $parameters = array();

        if (isset($params['data'])) {
            $parameters = $resource->getRouteParamsForInstance($params['data']);
        } elseif (isset($params['parent'])) {
            $parameters = $resource->getRouteParamsFromParent($params['parent']);
        }

        return $this->generator->generate(
            $resource->getRouteName($action->getName()),
            $parameters,
            $referenceType
        );
    }
}