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
        $builder->addRoute($action, 'bulk_delete', 'GET', $action->getUriPath('delete'));
        $builder->addRoute($action, 'bulk_remove', 'DELETE', $action->getUriPath(''));
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Action $action, UrlGeneratorInterface $generator, $data)
    {
        if (!$builder->getAction()) {
            $builder
                ->setMethod('DELETE')
                ->setAction($generator->generate(
                    $action->getRouteName('bulk_remove'),
                    $action->getRouteParams($data)
                ));
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('bulk', true);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'bulk_delete';
    }
}