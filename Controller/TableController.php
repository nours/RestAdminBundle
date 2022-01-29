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

use DomainException;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\TableBundle\Factory\TableFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TableController
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class TableController
{
    /**
     * @var TableFactory
     */
    private $tableFactory;

    public function __construct(TableFactory $tableFactory)
    {
        $this->tableFactory = $tableFactory;
    }

    public function __invoke(Request $request, DomainResource $resource, $parent = null)
    {
        if (!($tableName = $resource->getConfig('table'))) {
            throw new DomainException(sprintf('Resource %s has no table', $resource->getFullName()));
        }

        $table = $this->tableFactory->createTable($tableName, array(
            'resource'   => $resource,
            'route_data' => $parent
        ));

        return $table->handle($request)->createView();
    }
}