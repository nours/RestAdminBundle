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

/**
 * Class RestAdminEvents
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class RestAdminEvents
{
    /**
     * A route event enables to hook into rest admin route creation.
     *
     * Dispatched by the route builder before a route is created, in order to be able to change some defaults parameters.
     *
     * @see RouteEvent
     */
    const ROUTE = 'rest_admin.route';

    /**
     * Action config events are triggered when an action is built, and enables to update it's configuration.
     *
     * The main use of this event is to append handlers for actions.
     *
     * @see ActionConfigEvent
     */
    const ACTION_CONFIG = 'rest_admin.action_config';

    /**
     * Resource event is triggered juste before resource
     *
     * @see ActionConfigEvent
     */
    const RESOURCE_CONFIG = 'rest_admin.resource';
}