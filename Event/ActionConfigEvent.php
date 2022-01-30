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

/**
 * Class ActionConfigEvent
 *
 * @deprecated use ActionConfigurationEvent instead
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionConfigEvent extends ResourceEvent
{
    /**
     * @var string
     */
    private $actionName;
    private $actionType;

    /**
     * @var array
     */
    public $config;

    /**
     * @var array
     */
    public $handlers = [];

    /**
     * @param DomainResource $resource
     * @param string $actionName
     * @param string $actionType
     * @param array $config
     */
    public function __construct(
        DomainResource $resource,
        $actionName,
        $actionType,
        array $config
    ) {
        parent::__construct($resource);

        $this->actionName = $actionName;
        $this->actionType = $actionType;
        $this->config     = $config;
    }

    /**
     * Shortcut to add a handler to this action's config
     *
     * @param string $handler
     * @param int $priority
     */
    public function addHandler($handler, $priority)
    {
        $this->handlers[] = array($handler, $priority);
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @return string
     */
    public function getActionType()
    {
        return $this->actionType;
    }
}