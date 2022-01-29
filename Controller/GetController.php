<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Controller;

/**
 * Class GetController
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class GetController
{
    public function __invoke($data)
    {
        return $data;
    }
}