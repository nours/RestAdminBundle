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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Templating handler : translates non response controller results into template rendered responses.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class TemplatingHandler implements ViewHandler
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function supports(Request $request)
    {
        return $request->getRequestFormat() == 'html';
    }


    public function handle($data, Request $request)
    {
        // Find template from action
        $resource = $request->attributes->get('_resource');
        $action   = $request->attributes->get('_action');
        $template = $action->getTemplate();

        $parameters = array(
            'resource' => $resource,
            'action' => $action,
        );
        $responseStatus = Response::HTTP_OK;

        if ($data instanceof FormInterface) {
            $parameters['form'] = $data->createView();
            $parameters['data'] = $data->getData();

            // Change response status if any error
            if ($data->isSubmitted() && !$data->isValid()) {
                $responseStatus = Response::HTTP_BAD_REQUEST;
            }
        } elseif (is_array($data)) {
            $parameters = $data;
        } else {
            $parameters['data'] = $data;
        }

        // Render response
        $content = $this->getTemplating()->render($template, $parameters);

        $response = new Response($content, $responseStatus, array(
            'Content-Type' => $request->getMimeType($request->getRequestFormat())
        ));

        return $response;
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
     */
    private function getTemplating()
    {
        return $this->container->get('templating');
    }
}