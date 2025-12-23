<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Annotation;


use Symfony\Component\Routing\Attribute\DeprecatedAlias;

/**
 * Annotation Route.
 *
 * @Annotation
 * @Target({"METHOD"})
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Route // extends \Symfony\Component\Routing\Annotation\Route
{
    private ?string $path = null;
    private ?string $name = null;
    private array $requirements = [];
    private array $options = [];
    private array $defaults = [];
    private ?string $host = null;
    private array $methods = [];
    private array $schemes = [];
    private ?string $condition = null;
    
    public function __construct(array $data) {
        $data['path'] = $data['path'] ?? null;
        $data['name'] = $data['name'] ?? null;
        $data['requirements'] = $data['requirements'] ?? null;
        $data['options'] = $data['options'] ?? null;
        $data['defaults'] = $data['defaults'] ?? null;
        $data['host'] = $data['host'] ?? null;
        $data['methods'] = $data['methods'] ?? null;
        $data['schemes'] = $data['schemes'] ?? null;
        $data['condition'] = $data['condition'] ?? null;
        
        $data = array_filter($data, static function ($value): bool {
            return null !== $value;
        });
        
        if (isset($data['value'])) {
            $data['path'] = $data['value'];
            unset($data['value']);
        }
        
        foreach ($data as $key => $value) {
            $method = 'set'.str_replace('_', '', $key);
            if (!method_exists($this, $method)) {
                throw new \BadMethodCallException(sprintf('Unknown property "%s" on annotation "%s".', $key, static::class));
            }
            $this->$method($value);
        }
    }
    
    public function setPath(string $path)
    {
        $this->path = $path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setHost(string $pattern)
    {
        $this->host = $pattern;
    }
    
    public function getHost()
    {
        return $this->host;
    }
    
    public function setName(string $name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setRequirements(array $requirements)
    {
        $this->requirements = $requirements;
    }
    
    public function getRequirements()
    {
        return $this->requirements;
    }
    
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }
    
    public function getDefaults()
    {
        return $this->defaults;
    }
    
    public function setSchemes($schemes)
    {
        $this->schemes = \is_array($schemes) ? $schemes : [$schemes];
    }
    
    public function getSchemes()
    {
        return $this->schemes;
    }
    
    public function setMethods($methods)
    {
        $this->methods = \is_array($methods) ? $methods : [$methods];
    }
    
    public function getMethods()
    {
        return $this->methods;
    }
    
    public function setCondition(?string $condition)
    {
        $this->condition = $condition;
    }
    
    public function getCondition()
    {
        return $this->condition;
    }
    
    /**
     * Makes a config array from this route.
     * 
     * @return array
     */
    public function toArray(): array
    {
        return array(
            'path'      => $this->getPath(),
            'name'      => $this->getName(),
            'requirements' => $this->getRequirements(),
            'options'   => $this->getOptions(),
            'defaults'  => $this->getDefaults(),
            'host'      => $this->getHost(),
            'methods'   => $this->getMethods(),
            'schemes'   => $this->getSchemes(),
            'condition' => $this->getCondition()
        );
    }
}