<?php
/*
 * This file is part of NoursRestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Domain;
use Doctrine\Common\Inflector\Inflector;
use Symfony\Component\HttpFoundation\Request;

/**
 * Resource description.
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class Resource
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $routePrefix;
    private $basePrefix;


    /**
     * @var Action[]
     */
    private $actions = array();

    /**
     * @var Resource
     */
    private $parent;

    /**
     * The default form type name
     *
     * @var string
     */
    private $form;

    /**
     * The user defined resource factory.
     *
     * @var null|callback
     */
    private $factory;

//    /**
//     * Role used for basic access management.
//     *
//     * @var string
//     */
//    private $role;
//
//    /**
//     * The default table type name
//     *
//     * @var string
//     */
//    private $table;
//
//    /**
//     * The templates for the html based user interface
//     *
//     * @var array
//     */
//    private $templates = array();
//
//    /**
//     * The layout used for the user interface
//     *
//     * @var string
//     */
//    private $layout;

    /**
     * @param string $name
     * @param array $config
     */
    public function __construct($name, array $config)
    {
        $class = $config['class'];

        $this->class = $class;
        $this->identifier = isset($config['identifier']) ? $config['identifier'] : 'id';
        $this->name = $name;

        // Slug set by config or defaults to pluralized name
        if (isset($config['slug'])) {
            $this->setSlug($config['slug']);
        } else {
            $this->setSlug(Inflector::pluralize($name));
        }

        // Form
        if (isset($config['form'])) {
            $this->form = $config['form'];
        }

        // Parent
        if (isset($config['parent'])) {
            $this->parent = $config['parent'];
        }

        // Parent
        if (isset($config['factory'])) {
            $this->factory = $config['factory'];
        }

        $this->basePrefix = $name . '_';
        $this->routePrefix = $this->basePrefix;
    }

    /**
     * @return callable|null
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Full name, including parent paths.
     *
     * @return string
     */
    public function getFullName()
    {
        if (!empty($this->fullName)) {
            return $this->fullName;
        }

        $parts = array();

        if ($this->parent) {
            $parts[] = $this->parent;
        }

        $parts[] = $this->name;

        return $this->fullName = implode('.', $parts);
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return \Nours\RestAdminBundle\Domain\Resource
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $parent
     */
    public function setParent(Resource $parent)
    {
        $this->parent = $parent;

        $this->routePrefix = $parent->routePrefix . $this->basePrefix;
    }

//    /**
//     * @return string
//     */
//    public function getRole()
//    {
//        return $this->role;
//    }
//
//    /**
//     * @param string $role
//     */
//    public function setRole($role)
//    {
//        $this->role = $role;
//    }

    /**
     * @return string
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @param string $form
     */
    public function setForm($form)
    {
        $this->form = $form;
    }

//    /**
//     * @return string
//     */
//    public function getTable()
//    {
//        return $this->table;
//    }
//
//    /**
//     * @param string $table
//     */
//    public function setTable($table)
//    {
//        $this->table = $table;
//    }
//
//    /**
//     * @return array
//     */
//    public function getTemplates()
//    {
//        return $this->templates;
//    }
//
//    /**
//     * @param array $templates
//     */
//    public function setTemplates($templates)
//    {
//        $this->templates = $templates;
//    }
//
//    /**
//     * @return string
//     */
//    public function getLayout()
//    {
//        return $this->layout;
//    }
//
//    /**
//     * @param string $layout
//     */
//    public function setLayout($layout)
//    {
//        $this->layout = $layout;
//    }

    /**
     * Get route name for an action
     *
     * @param string $actionName
     * @return string
     */
    public function getRouteName($actionName)
    {
        return $this->routePrefix . $actionName;
    }


    /**
     * Get the relative uri path for this resource.
     *
     * @param string $suffix
     * @param boolean $objectPart If the object param is to be included
     * @return string
     */
    public function getUriPath($suffix = null, $objectPart = false)
    {
        $parts = array();

        if ($parent = $this->getParent()) {
            $parts[] = $parent->getResourceUriPath();
        }

        $parts[] = $this->getSlug();

        if ($objectPart) {
            $parts[] = '{' . $this->getName() . '}';
        }

        if ($suffix) {
            $parts[] = $suffix;
        }

        return implode('/', $parts);
    }

    /**
     * Get the relative uri path including the identifier param
     *
     * @param null $suffix
     * @return string
     */
    public function getResourceUriPath($suffix = null)
    {
        return $this->getUriPath($suffix, true);
    }

    /**
     * Formulae for generating event names.
     *
     * @param $eventName
     * @param Action $action
     * @return string
     */
    public function getEventName($eventName, Action $action)
    {
        return implode('.', array(
            $this->getFullName(), $action->getName(), $eventName
        ));
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasAction($name)
    {
        return isset($this->actions[$name]);
    }

    /**
     * @param string $name
     * @return Action
     */
    public function getAction($name)
    {
        return isset($this->actions[$name]) ? $this->actions[$name] : null;
    }

    /**
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param Action $action
     */
    public function addAction(Action $action)
    {
        $this->actions[$action->getName()] = $action;
    }

    /**
     * Build route parameters for an instance of this resource
     *
     * @param $data
     * @return array
     */
    public function getRouteParams($data)
    {
        if ($parent = $this->getParent()) {
            return $parent->getResourceRouteParams($this->getParentObject($data));
        }

        return array();
    }

    /**
     * Build route parameters for an instance of this resource
     *
     * @param $data
     * @return array
     */
    public function getResourceRouteParams($data)
    {
        $params = array(
            $this->getName() => $this->getObjectIdentifier($data)
        );

        if ($parent = $this->getParent()) {
            $params = array_merge($params, $parent->getResourceRouteParams($this->getParentObject($data)));
        }

        return $params;
    }

    /**
     * Retrieve parent object from data.
     *
     * @param $data
     * @return null
     */
    public function getParentObject($data)
    {
        if ($parent = $this->getParent()) {
            $name = $parent->getName();

            if (isset($data->$name)) {
                return $data->$name;
            } else {
                $method = 'get' . ucfirst($name);

                return $data->$method();
            }
        }

        return null;
    }

    /**
     * Returns an object id value
     *
     * @param $data
     * @return mixed
     */
    public function getObjectIdentifier($data)
    {
        $idMethod = 'get' . ucfirst($this->getIdentifier());
        return $data->$idMethod();
    }
}