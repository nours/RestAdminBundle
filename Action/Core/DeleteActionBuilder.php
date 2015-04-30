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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class DeleteActionBuilder
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class DeleteActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Resource $resource, Action $action)
    {
        $builder->addRoute($resource, $action, 'delete_form', 'GET', $resource->getResourceUriPath('delete'));
        $builder->addRoute($resource, $action, 'delete', 'DELETE', $resource->getUriPath());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Resource $resource, UrlGeneratorInterface $generator)
    {
        $routeName = $resource->getRouteName('delete');

        $builder
            ->setMethod('DELETE')
            ->setAction($generator->generate($routeName))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildAction(Action $action, array $options = array())
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'delete';
    }
}