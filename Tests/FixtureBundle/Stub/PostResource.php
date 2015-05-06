<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Stub;

use Nours\RestAdminBundle\Domain\Resource;

/**
 * Sample Resource for post fixtures
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class PostResource extends Resource
{
    public function __construct()
    {
        parent::__construct('post', array(
            'class' => 'Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post'
        ));
    }
}