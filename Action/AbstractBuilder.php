<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Action;

use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Route;

/**
 * Class AbstractAction
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
abstract class AbstractBuilder implements ActionBuilderInterface
{
    protected $template;
    protected $controller;

    /**
     * @param $template
     * @param $controller
     */
    public function __construct($template, $controller)
    {
        $this->template   = $template;
        $this->controller = $controller;
    }

    /**
     * {@inheritdoc}
     */
    public function createAction(Resource $resource, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'template', 'controller'
        ));
        $resolver->setDefaults(array(
            'template'   => $this->template,
            'controller' => $this->controller
        ));

        $this->setDefaultOptions($resolver);

        $options = $resolver->resolve($options);

        $action = new Action();
        $action->setName($this->getName());
        $action->setController($options['controller']);
        $action->setTemplate($options['template']);

        $this->buildAction($action, $options);

        return $action;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Resource $resource, UrlGeneratorInterface $generator)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function buildAction(Action $action, array $options = array())
    {

    }

    /**
     * Helper function to generate url base path for the resource.
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return string
     */
    protected function getBasePath(Resource $resource)
    {
        return '';
    }
}