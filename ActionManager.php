<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle;

use Nours\RestAdminBundle\Action\ActionBuilderInterface;


/**
 * Class ActionManager
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionManager
{
    /**
     * @var ActionBuilderInterface[]
     */
    private $actionBuilders = [];

    /**
     * @param ActionBuilderInterface $builder
     */
    public function addActionBuilder(ActionBuilderInterface $builder)
    {
        $this->actionBuilders[$builder->getName()] = $builder;
    }

    /**
     * @param string $type
     * @return bool
     */
    public function hasActionBuilder($type): bool
    {
        return isset($this->actionBuilders[$type]);
    }

    /**
     * @param string $type
     * @return ActionBuilderInterface
     */
    public function getActionBuilder(string $type): ActionBuilderInterface
    {
        if (!$this->hasActionBuilder($type)) {
            throw new \InvalidArgumentException(sprintf(
                "Action builder %s not registered (%s are)", $type, implode(', ', array_keys($this->actionBuilders))
            ));
        }
        return $this->actionBuilders[$type];
    }

    /**
     * @return ActionBuilderInterface
     */
    public function getCustomActionBuilder(): ActionBuilderInterface
    {
        if (!isset($this->actionBuilders['custom'])) {
            throw new \InvalidArgumentException(sprintf(
                "There is no custom action builder registered (%s are)", implode(', ', array_keys($this->actionBuilders))
            ));
        }

        return $this->actionBuilders['custom'];
    }

    /**
     * @return ActionBuilderInterface[]
     */
    public function getActionBuilders(): array
    {
        return $this->actionBuilders;
    }
}