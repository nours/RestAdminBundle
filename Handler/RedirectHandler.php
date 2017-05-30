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
use Nours\RestAdminBundle\Helper\AdminHelper;
use Symfony\Component\HttpFoundation\RedirectResponse;


/**
 * Redirection handler : generates a redirect response to resource index action.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RedirectHandler
{
    /**
     * @var AdminHelper
     */
    private $helper;

    /**
     * @param AdminHelper $helper
     */
    public function __construct(AdminHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param DomainResource $resource
     * @param mixed $data
     * @return RedirectResponse
     */
    public function handleRedirect(DomainResource $resource, $data)
    {
        if ($resource->isSingleResource()) {
            $url = $this->helper->generateUrl($resource->getParent()->getAction('index'), $data);
        } else {
            $url = $this->helper->generateUrl($resource->getAction('index'), $data);
        }

        return new RedirectResponse($url);
    }
}