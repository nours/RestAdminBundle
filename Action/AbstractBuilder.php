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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\Routing\Route;

/**
 * Class AbstractAction
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
abstract class AbstractBuilder implements ActionBuilderInterface
{
    /**
     * {@inheritdoc}
     */
    public function createAction(Resource $resource, array $options = array())
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(array(
            'template', 'controller'
        ));
//        $resolver->setDefaults(array('resource' => $resource));

        $this->setDefaultOptions($resolver, $resource);

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