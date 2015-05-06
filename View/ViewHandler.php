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

use Symfony\Component\HttpFoundation\Request;


/**
 * Class ViewHandler
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
interface ViewHandler
{
    public function supports(Request $request);


    public function handle($data, Request $request);
}