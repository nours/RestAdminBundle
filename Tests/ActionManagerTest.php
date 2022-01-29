<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests;

use Nours\RestAdminBundle\ActionManager;

/**
 * Class AdminManagerTest
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionManagerTest extends AdminTestCase
{
    /**
     * @var ActionManager
     */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->manager = $this->get('rest_admin.action_manager');
    }

    /**
     * The custom action builder is set in action manager.
     */
    public function testGetCustomActionBuilder()
    {
        $builder = $this->manager->getCustomActionBuilder();

        $this->assertInstanceOf('Nours\RestAdminBundle\Action\Core\CustomActionBuilder', $builder);
        $this->assertEquals('custom', $builder->getName());
    }

}