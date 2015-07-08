<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Controller;

use Nours\RestAdminBundle\Annotation as Rest;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Foo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FooController
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 *
 * @Rest\Resource(
 *  "Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Foo"
 * )
 *
 * @Rest\Action("get", template = "foo/get.html.twig")
 */
class FooController
{
    /**
     * @Rest\Action("index", template = "foo/index.html.twig")
     *
     * @return array
     */
    public function indexAction($data)
    {
        return array(
            'data' => $data
        );
    }

    /**
     * @param Request $request
     *
     * @Rest\ParamFetcher("index")
     */
    public function fetchParamsIndex(Request $request)
    {
        $foos = array(
            new Foo(1),
            new Foo(2),
            new Foo(3),
            new Foo(4),
            new Foo(5)
        );

        $request->attributes->set('data', $foos);
    }

    /**
     * @param Request $request
     *
     * @Rest\ParamFetcher()
     */
    public function fetchParamsDefault(Request $request)
    {
        if ($id = $request->attributes->get('foo')) {
            $request->attributes->set('data', new Foo($id));
        }
    }
}