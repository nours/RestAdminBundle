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

use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Domain\Action;

/**
 * Class ActionConfigEvent
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionConfigEvent extends ResourceEvent
{
    /**
     * @var string
     */
    private $actionName;

    /**
     * @var array
     */
    public $config;

    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param string $actionName
     * @param array $config
     */
    public function __construct(
        Resource $resource,
        $actionName,
        array $config
    ) {
        parent::__construct($resource);

        $this->actionName = $actionName;
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
        $this->config['handlers'][] = array($handler, $priority);
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
}