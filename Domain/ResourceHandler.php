<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Domain;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;

/**
 * A ResourceHandler
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceHandler
{
    private $resolver;

    public function __construct(ControllerResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }


    public function handleSuccess($data, FormInterface $form, Resource $resource, Action $action)
    {
        // Init a request for resolver
        $request = new Request(array(), array(), array(
            'resource' => $resource,
            'action'   => $action,
            'form'     => $form,
            'data'     => $data
        ));

        foreach ($action->getHandlers() as $handler) {
            $request->attributes->set('_controller', $handler);

            $controller = $this->resolver->getController($request);

            if ($controller === false) {
                throw new \DomainException(sprintf(
                    "Handler %s for resource %s and action %s could not be resolved",
                    $handler, $resource->getFullName(), $action->getName()
                ));
            }

            $arguments  = $this->resolver->getArguments($request, $controller);

            $response = call_user_func_array($controller, $arguments);

//            var_dump($response);die;

            if (!empty($response)) {
                return $response;
            }
        }

        return $form;
    }
}