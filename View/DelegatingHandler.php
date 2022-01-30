<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class DelegatingHandler
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DelegatingHandler implements ViewHandler
{
    /**
     * @var ViewHandler[]
     */
    private $handlers;

    /**
     * @param ViewHandler $handler
     */
    public function addHandler(ViewHandler $handler)
    {
        $this->handlers[] = $handler;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request): bool
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($request)) {
                return true;
            }
        }

        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function handle($data, Request $request): ?Response
    {
        foreach ($this->handlers as $handler) {
            if ($handler->supports($request)) {
                return $handler->handle($data, $request);
            }
        }
    }
}