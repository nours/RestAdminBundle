<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Annotation;


/**
 * Annotation Route.
 *
 * @Annotation
 * @Target({"METHOD"})
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Route extends \Symfony\Component\Routing\Annotation\Route
{
    /**
     * Makes a config array from this route.
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array(
            'path'      => $this->getPath(),
            'name'      => $this->getName(),
            'requirements' => $this->getRequirements(),
            'options'   => $this->getOptions(),
            'defaults'  => $this->getDefaults(),
            'host'      => $this->getHost(),
            'methods'   => $this->getMethods(),
            'schemes'   => $this->getSchemes(),
            'condition' => $this->getCondition()
        );
        
        return $array;
    }
}