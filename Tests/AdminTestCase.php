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

use Nours\RestAdminBundle\AdminManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\FileLocator;

/**
 * Class AdminTestCase
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminTestCase extends WebTestCase
{
    /**
     * @return AdminManager
     */
    protected function getAdminManager()
    {
        return $this->getContainer()->get('rest_admin.manager');
    }

    /**
     * @param string $service
     * @return mixed
     */
    protected function get($service)
    {
        return $this->getContainer()->get($service);
    }

    /**
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    protected function getClient()
    {
        $client = static::createClient();
        return $client;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        if (empty(static::$kernel)) {
            static::bootKernel();
        }

        if (!static::$kernel->getContainer()) {
            static::$kernel->boot();
        }

        return static::$kernel->getContainer();
    }

    /**
     * Returns a file locator for test config files.
     *
     * @return FileLocator
     */
    protected function getFileLocator()
    {
        $locator = new FileLocator(array(
            __DIR__ . '/app/config'
        ));

        return $locator;
    }



    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

}