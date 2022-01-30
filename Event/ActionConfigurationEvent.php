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

use Nours\RestAdminBundle\Domain\Action;


/**
 * Class ActionConfigurationEvent
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionConfigurationEvent extends ResourceEvent
{
    private $action;

    public function __construct(Action $action) {
        parent::__construct($action->getResource());

        $this->action = $action;
    }

    /**
     * @return Action
     */
    public function getAction(): Action
    {
        return $this->action;
    }

    /**
     * Shortcut to add a handler to this action's config
     *
     * @param string $handler
     * @param int $priority
     */
    public function addHandler(string $handler, int $priority)
    {
        $handlers = $this->action->getConfig('handlers');
        $handlers[] = array($handler, $priority);

        $this->action->setConfig('handlers', $handlers);
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function updateConfig(string $key, $value)
    {
        $this->action->setConfig($key, $value);
    }
}