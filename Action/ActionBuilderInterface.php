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
use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ActionBuilderInterface
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
interface ActionBuilderInterface
{
    /**
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param array $options
     * @return Action
     */
    public function createAction(Resource $resource, array $options = array());

    /**
     * Override to provide default options for
     *
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver);

    /**
     * @param RoutesBuilder $builder
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     */
    public function buildRoutes(RoutesBuilder $builder, Resource $resource, Action $action);

    /**
     * Builds a form for this action.
     *
     * Responsible for initializing form action and method. As form needs absolute url, a UrlGeneratorInterface
     * is provided to generate them.
     *
     * @param FormBuilderInterface $builder
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param UrlGeneratorInterface $generator
     * @param mixed $model
     */
    public function buildForm(FormBuilderInterface $builder, Resource $resource, UrlGeneratorInterface $generator, $model);

    /**
     * @return string
     */
    public function getName();

    /**
     * Must return event triggered by this action
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @return array
     */
//    public function registerTriggeredEvent(Resource $resource);
}