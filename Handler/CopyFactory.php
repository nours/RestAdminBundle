<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Handler;


/**
 * Handler for ORM entities, for creation, update and delete actions.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CopyFactory
{
    public function factory($data)
    {
        return clone $data;
    }
}