<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Api;

use Nours\RestAdminBundle\Domain\Resource;


/**
 * Class DomainEvents
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ApiEvents
{
    /**
     * Requests loading a collection of resources
     */
    const EVENT_LOAD = 'load';

    /**
     * Requests loading a single resource
     */
    const EVENT_GET = 'get';

    /**
     * Requests resource creation
     */
    const EVENT_CREATE = 'create';

    /**
     * Requests data rendering
     */
    const EVENT_VIEW = 'view';

    /**
     * When forms are successful
     */
    const EVENT_SUCCESS = 'success';

    /**
     * Erroneous form validation
     */
    const EVENT_ERROR = 'error';

//    /**
//     * Request event occurs when trying to access any resource.
//     *
//     * Event class is ViewEvent
//     */
//    const REQUEST_EVENT  = 'request';
//
//    /**
//     * Response events are dispatched when a response should be rendered.
//     *
//     * Event class is ViewEvent
//     */
//    const RESPONSE_EVENT = 'response';
//
//    /**
//     * Occurs when a form has been submitted with success.
//     */
//    const SUCCESS_EVENT = 'success';
//
//    /**
//     * Occurs when a form validation fails.
//     */
//    const ERROR_EVENT = 'error';




//    /**
//     * Resource level index events are triggered when the user requests a resource global page.
//     *
//     * Event implementations should find a collection of elements to render.
//     */
//    const INDEX_EVENT = 'index';
//
//    /**
//     * Resource level get events are triggered on requesting a single resource object (either for rendering data, or process forms).
//     *
//     * Implementations will load the object according to the action's configuration.
//     */
//    const GET_EVENT   = 'get';
//
//    /**
//     * Success events occurs when forms should be rendered.
//     */
//    const FORM_EVENT   = 'form';
//
//    /**
//     * Success events occurs after form has been validated with success.
//     */
//    const SUCCESS_EVENT   = 'success';
//
//    /**
//     * Error events occur when form validation fails.
//     */
//    const ERROR_EVENT     = 'error';

}