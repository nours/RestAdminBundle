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
    /**
     * @var SerializerInterface|\Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;
    private $serializationContext;

    public function __construct($serializer, $serializationContext = null)
    {
        $this->serializer = $serializer;
        $this->serializationContext = $serializationContext;
    }


    public function supports(Request $request)
    {
        return $request->getRequestFormat() == 'json';
    }


    public function handle($data, Request $request)
    {
        $responseStatus = Response::HTTP_OK;

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

        $serialized = $this->serializer->serialize($data, 'json', $this->serializationContext);

        return new Response($serialized, $responseStatus, array(
            'Content-Type' => $request->getMimeType('json')
        ));
    }
}