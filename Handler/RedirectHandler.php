<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Handler;

use Nours\RestAdminBundle\Domain\DomainResource;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Redirection handler : generates a redirect response to resource index action.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RedirectHandler
{
    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    /**
     * @param UrlGeneratorInterface $generator
     */
    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * @param DomainResource $resource
     * @param $data
     * @return RedirectResponse
     */
    public function handleRedirect(DomainResource $resource, $data)
    {
        $url = $this->generator->generate($resource->getRouteName('index'), $resource->getRouteParamsFromData($data));

        return new RedirectResponse($url);
    }
}