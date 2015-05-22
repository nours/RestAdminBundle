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

    public function setUp()
    {
        parent::setUp();

        $this->manager = $this->get('rest_admin.action_manager');
    }

    /**
     * The resources from app/config.resources.yml should be loaded
     */
    public function testGetDefaultActionBuilder()
    {
        $builder = $this->manager->getDefaultActionBuilder();

        $this->assertInstanceOf('Nours\RestAdminBundle\Action\Core\DefaultActionBuilder', $builder);
        $this->assertEquals('default', $builder->getName());
    }

}