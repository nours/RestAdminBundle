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
use Nours\RestAdminBundle\Domain\DomainResource;
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
     * @param DomainResource $resource
     * @param array $options
     * @return Action
     */
    public function createAction(DomainResource $resource, array $options = []): Action;

    /**
     * Override to provide default options for
     *
     * @param OptionsResolver $resolver
     */
    public function setDefaultOptions(OptionsResolver $resolver);

    /**
     * @param RoutesBuilder $builder
     * @param Action $action
     */
    public function buildRoutes(RoutesBuilder $builder, Action $action);

    /**
     * Builds a form for this action.
     *
     * Responsible for initializing form action and method. As form needs absolute url, a UrlGeneratorInterface
     * is provided to generate them.
     *
     * @param FormBuilderInterface $builder
     * @param Action $action
     * @param UrlGeneratorInterface $generator
     * @param mixed $data
     */
    public function buildForm(FormBuilderInterface $builder, Action $action, UrlGeneratorInterface $generator, $data);

    /**
     * @return string
     */
    public function getName(): string;
}