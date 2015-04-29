<?php
/*
 * This file is part of AdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nours\RestAdminBundle\NoursRestAdminBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle()
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_test.yml');
    }

//    /**
//     * @return string
//     */
//    public function getCacheDir()
//    {
//        $cacheDir = sys_get_temp_dir().'/nours-restrestadmin/cache';
//        if (!is_dir($cacheDir)) {
//            mkdir($cacheDir, 0777, true);
//        }
//
//        return $cacheDir;
//    }
//
//    /**
//     * @return string
//     */
//    public function getLogDir()
//    {
//        $logDir = sys_get_temp_dir().'/nours-restrestadmin/logs';
//        if (!is_dir($logDir)) {
//            mkdir($logDir, 0777, true);
//        }
//
//        return $logDir;
//    }
}