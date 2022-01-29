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
use Doctrine\ORM\EntityManager;
use Nours\RestAdminBundle\AdminManager;
use Nours\RestAdminBundle\Tests\FixtureBundle\DataFixtures\SqlitePurger;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
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
    /**
     * @return AdminManager
     */
    protected function getAdminManager(): AdminManager
    {
        return static::getContainer()->get('rest_admin.manager');
    }

    /**
     * @param string $service
     * @return mixed
     */
    protected function get($service)
    {
        return static::getContainer()->get($service);
    }

//    /**
//     * @return KernelBrowser
//     */
//    protected function getClient(): KernelBrowser
//    {
//        return static::createClient();
//    }

    /**
     * Returns a file locator for test config files.
     *
     * @return FileLocator
     */
    protected function getFileLocator()
    {
        return new FileLocator(array(
            __DIR__ . '/app/config'
        ));
    }


    /**
     * Executes fixtures
     */
    protected function loadFixtures()
    {
        $loader = new Loader();
        $loader->loadFromDirectory(__DIR__ . '/FixtureBundle/Fixtures');

        $purger = new SqlitePurger();
        $executor = new ORMExecutor($this->getEntityManager(), $purger);
        $executor->execute($loader->getFixtures());
    }

    /**
     * Returns the doctrine orm entity manager
     *
     * @return EntityManager
     */
    protected function getEntityManager(): EntityManager
    {
        return static::getContainer()->get('doctrine.orm.entity_manager');
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

    protected function assertSuccessful(Response $response)
    {
        if (!$response->isSuccessful()) {
//            file_put_contents('dump.html', $response->getContent());

            $this->fail(sprintf("Failed assert response successful (%s).\n", $response->getStatusCode()));
        }
    }
}