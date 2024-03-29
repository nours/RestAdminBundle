<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\EventListener;

use Nours\RestAdminBundle\EventListener\RequestListener;
use Nours\RestAdminBundle\Tests\AdminTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class RequestListenerTest
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RequestListenerTest extends AdminTestCase
{

    public function testOnKernelRequest()
    {
        $listener = new RequestListener($this->getAdminManager());

        $request = new Request();
        $request->attributes->set('_resource', 'post');
        $request->attributes->set('_action', 'create');
        $request->headers->set('Accept', 'application/json');

        $event = new RequestEvent($this->get('http_kernel'), $request, HttpKernelInterface::MAIN_REQUEST);

        $listener->onKernelRequest($event);

        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\DomainResource', $request->attributes->get('resource'));
        $this->assertInstanceOf('Nours\RestAdminBundle\Domain\Action', $request->attributes->get('action'));
        $this->assertEquals('json', $request->getRequestFormat());
    }

}