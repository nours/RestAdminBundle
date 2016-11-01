<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Tests\FixtureBundle\Action;

use Nours\RestAdminBundle\Action\AbstractBuilder;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Nours\RestAdminBundle\Tests\FixtureBundle\Entity\Comment;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class EditActionBuilder
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class PublishActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Action $action)
    {
        $resource = $action->getResource();
        $builder->addRoute($action, 'publish_form', 'GET', $resource->getResourceUriPath('publish'));
        $builder->addRoute($action, 'publish', 'PUT', $resource->getResourceUriPath('publish'));
        $builder->addRoute($action, 'unpublish', 'DELETE', $resource->getResourceUriPath('publish'));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Action $action, UrlGeneratorInterface $generator, $model)
    {
        if (!$model instanceof Comment) {
            throw new \InvalidArgumentException("Comment expected !");
        }

        $routeName = $action->getResource()->getRouteName('publish');

        $builder
            ->setMethod('PUT')
            ->setAction($generator->generate($routeName))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template'   => '',
            'controller' => '',
            'form'       => '',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'publish';
    }
}