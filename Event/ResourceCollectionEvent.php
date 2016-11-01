<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Event;

use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Domain\ResourceCollection;

/**
 * Class ResourceCollectionEvent
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceCollectionEvent extends ResourceEvent
{
    /**
     * @var ResourceCollection
     */
    private $collection;

    public function __construct(DomainResource $resource, ResourceCollection $collection)
    {
        parent::__construct($resource);

        $this->collection = $collection;
    }

    /**
     * @return ResourceCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }
}