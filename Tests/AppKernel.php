<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests;

use Doctrine;
use JMS;
use Knp;
use Nours;
use Symfony;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nours\RestAdminBundle\NoursRestAdminBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Nours\RestAdminBundle\Tests\FixtureBundle\FixtureBundle(),
            new Nours\TableBundle\NoursTableBundle()
        );
    }
    
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config/config_test.yml');
    }
}