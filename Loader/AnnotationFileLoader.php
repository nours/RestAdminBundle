<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Loader;

use Doctrine\Common\Annotations\Reader;
use Nours\RestAdminBundle\Domain\ResourceCollection;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Resource\FileResource;

/**
 * Load resources from controller file
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AnnotationFileLoader extends Loader
{
    /**
     * @var AnnotationClassLoader
     */
    private $loader;
    /**
     * @var FileLocatorInterface
     */
    private $locator;


    public function __construct(
        FileLocatorInterface $locator,
        AnnotationClassLoader $loader
    ) {
        parent::__construct();

        $this->locator = $locator;
        $this->loader = $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null): ResourceCollection
    {
        $path = $this->locator->locate($resource);

        $className = $this->findClass($path);

        return $this->loader->load($className);
    }


    private function findClass($path): ?string
    {
        $tokens = token_get_all(file_get_contents($path));

        $className = '';
        $count = count($tokens);
        $namespace = $class = false;
        for ($i = 0 ; $i < $count ; ++$i) {
            $token = $tokens[$i];

            if (!is_array($token)) {
                continue;
            }

            if ($namespace && T_STRING == $token[0]) {
                // After namespace, read NS_SEPARATOR and STRING
                do {
                    $className .= $token[1];
                    $token = $tokens[++$i];     // Inc token
                } while (
                    $i < $count && is_array($token) &&
                    in_array($token[0], array(T_NS_SEPARATOR, T_STRING))
                );
                $namespace = false;
            }

            if ($class && T_STRING == $token[0]) {
                // Found class name
                $className .= '\\' . $token[1];
                return $className;
            }

            if (T_CLASS == $token[0]) {
                $class = true;
            }

            if (T_NAMESPACE == $token[0]) {
                $namespace = true;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, string $type = null): bool
    {
        try {
            $path = $this->locator->locate($resource);
        } catch (\Exception $e) {
            return false;
        }

        return is_string($resource) && is_file($path) && (!$type || 'annotation' === $type);
    }
}