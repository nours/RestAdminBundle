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
    private $actionBuilders = array();

    /**
     * @param ActionBuilderInterface $builder
     */
    public function addActionBuilder(ActionBuilderInterface $builder)
    {
        $this->actionBuilders[$builder->getName()] = $builder;
    }

    /**
     * @param string $name
     * @return ActionBuilderInterface
     */
    public function getActionBuilder($name)
    {
        if (!isset($this->actionBuilders[$name])) {
            throw new \InvalidArgumentException(sprintf(
                "Action builder %s not registered (%s)", $name, implode(', ', array_keys($this->actionBuilders))
            ));
        }
        return $this->actionBuilders[$name];
    }

    /**
     * @return ActionBuilderInterface[]
     */
    public function getActionBuilders()
    {
        return $this->actionBuilders;
    }
}