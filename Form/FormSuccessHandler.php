<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Form;

use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * Entry point for form success handling.
 *
 * Delegates to action success handler, which will be executed in their priority order until
 * one returns a response (which can be anything).
 *
 * The handlers are invoked using SF2 Controller Resolver, which enables to use standard syntax
 * for handlers definitions.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FormSuccessHandler
{
    private $resolver;

    public function __construct(ControllerResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @param $data
     * @param Request $request
     * @param FormInterface $form
     * @param Action $action
     * @return mixed|FormInterface
     */
    public function handle($data, Request $request, FormInterface $form, Action $action)
    {
        // Init a request for resolver
        $request = $request->duplicate(array(), array(), array_replace($request->attributes->all(), array(
            'resource' => $action->getResource(),
            'action'   => $action,
            'form'     => $form,
            'data'     => $data
        )));

        foreach ($action->getHandlers() as $handler) {
            $request->attributes->set('_controller', $handler);

            $controller = $this->resolver->getController($request);

            if ($controller === false) {
                throw new \DomainException(sprintf(
                    "Handler %s for resource %s and action %s could not be resolved",
                    $handler, $action->getResource()->getFullName(), $action->getName()
                ));
            }

            $arguments  = $this->resolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);

            if (!empty($response)) {
                return $response;
            }
        }

        return $form;
    }
}