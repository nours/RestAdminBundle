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

use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\Serializer;
use Nours\RestAdminBundle\Api\ApiEvents;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Nours\RestAdminBundle\Api\Event\EventSubscriber;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class JsonSubscriber
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class JsonSubscriber implements EventSubscriber
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function getSubscribedEvents()
    {
        return array(
            ApiEvents::EVENT_VIEW  => array('render', 0)
        );
    }

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function render(ApiEvent $event)
    {
        if ($event->getRequest()->getRequestFormat() !== 'json') {
            return;
        }

        $serialized = $this->serializer->serialize($event->getModel(), 'json');

        $response = new Response($serialized);
        $response->headers->set('Content-Type', 'application/json');

        $event->setResponse($response);
    }
}