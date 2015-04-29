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
use Nours\RestAdminBundle\Api\ApiEvents;
use Nours\RestAdminBundle\Api\Event\ApiEvent;
use Nours\RestAdminBundle\Api\Event\EventSubscriber;
use Nours\RestAdminBundle\Doctrine\DoctrineRepository;


/**
 * Class DoctrineEventSubscriber
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DoctrineSubscriber implements EventSubscriber
{
    /**
     * @var DoctrineRepository
     */
    private $repository;

    public function getSubscribedEvents()
    {
        return array(
            ApiEvents::EVENT_LOAD => 'onLoad',
            ApiEvents::EVENT_GET  => array('onGet', 0)
        );
    }

    public function __construct(DoctrineRepository $repository)
    {
        $this->repository = $repository;
    }

    public function onLoad(ApiEvent $event)
    {
        $resource = $event->getResource();

        if ($this->repository->supports($resource)) {
            $object = $this->repository->find($resource, $event->getRequest());
//
//        $event->setModel($object);
        }
    }

    public function onGet(ApiEvent $event)
    {
        $resource = $event->getResource();

        if ($this->repository->supports($resource)) {

        }
    }
}