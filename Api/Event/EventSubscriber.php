<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Api\Event;

/**
 * Interface EventSubscriber
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
interface EventSubscriber
{
    /**
     * Returns and array of event => listeners
     *
     * @return array
     */
    public function getSubscribedEvents();
}