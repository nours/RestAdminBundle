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
 * Class BulkDeleteActionBuilder
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class BulkDeleteActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Action $action)
    {
        $resource = $action->getResource();
        $builder->addRoute($resource, $action, 'bulk_delete', 'GET', $resource->getUriPath('delete'));
        $builder->addRoute($resource, $action, 'bulk_remove', 'DELETE', $resource->getUriPath());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Action $action, UrlGeneratorInterface $generator, $data)
    {
        if (!$builder->getAction()) {
            $resource = $action->getResource();
            $routeName = $resource->getRouteName('bulk_remove');

            $builder
                ->setMethod('DELETE')
                ->setAction($generator->generate($routeName, $resource->getCollectionRouteParams($data)));
            ;
        }
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
        return 'bulk_delete';
    }
}