<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Menu\Helper;

use Knp\Menu\ItemInterface;
use Nours\RestAdminBundle\AdminManager;

/**
 * Experimental helper for adding resource specific items to menus.
 *
 * The createResourceMenuItem adds an item for the index action of the resource, which will match stricly the index route.
 *
 * It will receive a non-displayed children which will match any other resource route.
 *
 * @see \Nours\RestAdminBundle\Menu\Voter\ResourceRouteVoter
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ResourceMenuHelper
{
    private $adminManager;

    public function __construct(AdminManager $adminManager)
    {
        $this->adminManager = $adminManager;
    }


    public function createResourceMenuItem(
        ItemInterface $menu,
        $resourceName,
        $childName,
        array $childOptions = []
    ): ItemInterface {
        $resource = $this->adminManager->getResource($resourceName);

        $route = $resource->getRouteName('index');

        $childOptions['route'] = $route;
        $extras = $childOptions['extras'] ?? array();
        $childOptions['extras'] = $extras;

        // Add index action item
        $item = $menu->addChild($childName, $childOptions);

        // Add resource item as child
        $item->addChild($childName . '_stub', array(
            'display' => false,
            'extras' => array(
                'resource' => $resource
            )
        ));

        return $item;
    }
}