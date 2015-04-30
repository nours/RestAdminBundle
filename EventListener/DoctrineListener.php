<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\EventListener;

use Doctrine\Common\Persistence\ObjectRepository;
use Nours\RestAdminBundle\Api\ApiEvents;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Nours\RestAdminBundle\Doctrine\DoctrineRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;


/**
 * Class DoctrineListener
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineListener implements EventSubscriberInterface
{
    /**
     * @var RegistryInterface
     */
    private $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        /** @var \Nours\RestAdminBundle\Domain\Resource $resource */
        $resource = $request->attributes->get('_resource');

        if ($resource) {
            $class = $resource->getClass();

            if ($em = $this->doctrine->getManagerForClass($class)) {
            }
        }
    }


    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 20))
        );
    }
}