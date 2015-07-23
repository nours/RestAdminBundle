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

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class ViewHandler
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class JsonHandler implements ViewHandler
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function supports(Request $request)
    {
        return $request->getRequestFormat() == 'json';
    }


    public function handle($data, Request $request)
    {
        $responseStatus = Response::HTTP_OK;

        // todo : test this

        if ($data instanceof FormInterface) {
            // Change response status if any error
            if ($data->isSubmitted() && !$data->isValid()) {
                $responseStatus = Response::HTTP_BAD_REQUEST;
                $data = $data->getErrors();
            } else {
                // Display form data if is valid
                $data = $data->getData();
            }
        }

        $serializer = $this->getSerializer();
        $arguments = array($data, 'json');

        if ($serializer instanceof SerializerInterface && ($context = $this->getSerializationContext())) {
            $arguments[] = $context;
        }

        $serialized = call_user_func_array(array($serializer, 'serialize'), $arguments);

        return new Response($serialized, $responseStatus, array(
            'Content-Type' => $request->getMimeType($request->getRequestFormat())
        ));
    }

    /**
     * @return SerializationContext
     */
    private function getSerializationContext()
    {
        return $this->container->has('rest_admin.serialization_context') ?
            $this->container->get('rest_admin.serialization_context') :
            null;
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return $this->container->get('rest_admin.serializer');
    }
}