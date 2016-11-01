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

use Doctrine\Common\Annotations\Annotation;

/**
 * Class can either be passed as default or as class argument.
 *
 * @deprecated Use DomainResource
 *
 * @Annotation
 * @Target("CLASS")
 */
class Resource extends DomainResource
{
    
}
