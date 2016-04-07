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

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Nours\RestAdminBundle\AdminManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AdminTestCase
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminTestCase extends WebTestCase
{

    public function setUp()
    {


        parent::setUp();
    }

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
     * Executes fixtures
     */
    protected function loadFixtures()
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/FixtureBundle/Fixtures');

        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * Returns the doctrine orm entity manager
     *
     * @return EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Asserts the content type header of a response
     *
     * @param $expected
     * @param Response $response
     */
    protected function assertResponseContentType($expected, Response $response)
    {
        foreach ($response->headers as $header => $value) {
            $value = $value[0];
            if ($header == 'content-type' && $value !== $expected) {
                $this->fail("Response content type failed ($expected expected, $value found");
            }
        }
    }


    protected function assertRedirect(Response $response, $location, $statusCode = 302)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(),
            "Bad redirect status ($statusCode expected, {$response->getStatusCode()} found)."
        );

        if ($response->headers->has('Location')) {
            $this->assertEquals($location, $loc = $response->headers->get('Location'),
                "Response location $loc do not match ($location expected)");
        } else {
            $this->fail("Response has no header Location.");
        }

    }
}