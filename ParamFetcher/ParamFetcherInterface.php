<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\ParamFetcher;

use Symfony\Component\HttpFoundation\Request;

/**
 *
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
interface ParamFetcherInterface
{
    public function fetch(Request $request): void;
}