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

use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        if ($data instanceof FormInterface) {
            $data = $data->getData();
        }

        $serialized = $this->getSerializer()->serialize($data, 'json');

        return new Response($serialized, Response::HTTP_OK, array(
            'Content-Type' => $request->getMimeType($request->getRequestFormat())
        ));
    }

    /**
     * @return Serializer
     */
    private function getSerializer()
    {
        return $this->container->get('rest_admin.serializer');
    }
}