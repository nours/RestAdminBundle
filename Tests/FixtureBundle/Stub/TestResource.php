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

use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Post;

/**
 * Sample Resource for test fixtures
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class TestResource extends DomainResource
{
    public function __construct()
    {
        parent::__construct(Post::class, array(
            'name' => 'test'
        ));
    }
}