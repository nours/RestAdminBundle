<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Action\Core;

use Nours\RestAdminBundle\Action\AbstractBuilder;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class GetAction
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class CreateActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Resource $resource, Action $action)
    {
        $builder->addRoute($resource, $action, 'create_form', 'GET', $resource->getUriPath('new'));
        $builder->addRoute($resource, $action, 'create', 'POST', $resource->getUriPath());
    }

    /**
     * {@inheritdoc}
     */
    public function buildAction(Action $action, array $options = array())
    {
        $action->setForm($options['form']);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver, Resource $resource)
    {
        $resolver->setDefaults(array(
            'template' => '@NoursRestAdminBundle:core:create.html.twig',
            'controller' => 'rest_admin.core_controller:formAction',
            'form' => $resource->getForm()
        ));
        $resolver->setRequired('form');
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'create';
    }
}