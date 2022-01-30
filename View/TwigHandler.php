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

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\DomainResource;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;


/**
 * Templating handler : translates non response controller results into template rendered responses.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class TwigHandler implements ViewHandler
{
    private $twig;
    private $formats;

    public function __construct(Environment $twig, array $formats)
    {
        $this->twig = $twig;
        $this->formats = $formats;
    }


    public function supports(Request $request): bool
    {
        return in_array($request->getRequestFormat(), $this->formats);
    }


    public function handle($data, Request $request): ?Response
    {
        // Find template from action
        /** @var DomainResource $resource */
        $resource = $request->attributes->get('resource');
        /** @var Action $action */
        $action   = $request->attributes->get('action');

        // If the request has no resource nor action, return the data
        if (!$resource || !$action) {
            return $data;
        }

        $template = $action->getTemplate();

        if (empty($template)) {
            throw new \DomainException(sprintf(
                'The action %s do not have default template. ' .
                'Either set a default template in action builder, or manually render one in controller %s',
                $action->getName(), $action->getController()
            ));
        }

        $parameters = array(
            'resource' => $resource,
            'action' => $action,
        );
        $responseStatus = Response::HTTP_OK;

        if ($data instanceof FormInterface) {
            $parameters['form'] = $data->createView();
            $parameters['data'] = $data->getData();
        } elseif (is_array($data)) {
            $parameters = array_merge($parameters, $data);
        } else {
            $parameters['data'] = $data;
        }

        // Render response
        $content = $this->twig->render($template, $parameters);

        return new Response($content, $responseStatus, array(
            'Content-Type' => $request->getMimeType($request->getRequestFormat())
        ));
    }
}