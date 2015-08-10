<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\ParamFetcher;

use Nours\RestAdminBundle\ParamFetcher\ParamFetcherInterface;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Foo;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FooParamFetcher
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FooParamFetcher implements ParamFetcherInterface
{
    public function fetch(Request $request)
    {
        if ($id = $request->attributes->get('foo')) {
            $request->attributes->set('data', new Foo($id));
        }
    }
}