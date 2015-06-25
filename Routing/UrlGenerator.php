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
        $this->generator = $generator;
    }

    /**
     * @param Action $action
     * @param array $params
     * @param bool $referenceType
     * @return string
     */
    public function generate(Action $action, array $params = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        $resource = $action->getResource();
        $parameters = array();

        if (isset($params['data'])) {
            $parameters = $resource->getResourceRouteParams($params['data']);
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