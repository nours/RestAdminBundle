<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Persistance;


use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\HttpFoundation\Request;

interface Repository
{
    /**
     * Finds an entity object based on request parameters and resource properties.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Request $request
     * @return mixed
     */
    public function find(Resource $resource, Request $request);

    /**
     * @param Resource $resource
     * @return boolean If the repository can load this class
     */
    public function supports(Resource $resource);
}