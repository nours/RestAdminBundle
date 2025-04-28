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
        $tokens = token_get_all(file_get_contents($path));
        
        $nsTokens = [\T_NS_SEPARATOR => true, \T_STRING => true];
        if (\defined('T_NAME_QUALIFIED')) {
            $nsTokens[\T_NAME_QUALIFIED] = true;
        }
        
        for ($i = 0 ; $i < $count ; ++$i) {
            $token = $tokens[$i];
            if (!isset($token[1])) {
                continue;
            }
            
            if (true === $class && \T_STRING === $token[0]) {
                return $namespace.'\\'.$token[1];
            }
            
            if (true === $namespace && isset($nsTokens[$token[0]])) {
                $namespace = $token[1];
                while (isset($tokens[++$i][1], $nsTokens[$tokens[$i][0]])) {
                    $namespace .= $tokens[$i][1];
                }
                $token = $tokens[$i];
            }
            
            if (\T_CLASS === $token[0]) {
                // Skip usage of ::class constant and anonymous classes
                $skipClassToken = false;
                for ($j = $i - 1; $j > 0; --$j) {
                    if (!isset($tokens[$j][1])) {
                        if ('(' === $tokens[$j] || ',' === $tokens[$j]) {
                            $skipClassToken = true;
                        }
                        break;
                    }
                    
                    if (\T_DOUBLE_COLON === $tokens[$j][0] || \T_NEW === $tokens[$j][0]) {
                        $skipClassToken = true;
                        break;
                    } elseif (!\in_array($tokens[$j][0], [\T_WHITESPACE, \T_DOC_COMMENT, \T_COMMENT])) {
                        break;
                    }
                }
                
                if (!$skipClassToken) {
                    $class = true;
                }
            }
            
            if (\T_NAMESPACE === $token[0]) {
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