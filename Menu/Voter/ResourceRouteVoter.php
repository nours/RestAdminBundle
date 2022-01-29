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
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param Request $request
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function matchItem(ItemInterface $item): ?bool
    {
        /** @var DomainResource $itemResource */
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
    private function getRequestResource(): ?DomainResource
    {
        $request = $this->requestStack->getCurrentRequest();

        return $request ? $request->attributes->get('resource') : null;
    }
}