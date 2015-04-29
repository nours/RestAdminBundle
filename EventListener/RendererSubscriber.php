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
use Symfony\Component\HttpFoundation\Response;


/**
 * Class DoctrineEventSubscriber
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RendererSubscriber implements EventSubscriber
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function getSubscribedEvents()
    {
        return array(
            ApiEvents::EVENT_VIEW  => array('render', 0)
        );
    }

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(ApiEvent $event)
    {
        if ($event->getRequest()->getRequestFormat() !== 'html') {
            return;
        }

        $template = $this->twig->loadTemplate($event->getAction()->getTemplate());

        $arguments = array(
            'resource' => $event->getResource(),
            'action' => $event->getAction(),
            'model' => $event->getModel(),
            'form' => $event->getForm()
        );

        $response = new Response($template->render($arguments));

        $event->setResponse($response);
    }
}