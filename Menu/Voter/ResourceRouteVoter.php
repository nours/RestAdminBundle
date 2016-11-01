<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Menu\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Nours\RestAdminBundle\Domain\DomainResource;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ResourceRouteVoter.
 *
 * Matches an item using a resource index route.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceRouteVoter implements VoterInterface
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item)
    {
        if ($itemResource = $item->getExtra('resource')) {
            if ($requestResource = $this->getRequestResource()) {
                return $itemResource === $requestResource;
            }
        }

        return null;
    }

    /**
     * @return DomainResource
     */
    private function getRequestResource()
    {
        if (empty($this->request)) {
            return null;
        }

        return $this->request->attributes->get('resource');
    }
}