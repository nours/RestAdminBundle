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
use Nours\RestAdminBundle\Routing\RoutesBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class FormActionBuilder.
 *
 * Implement the standard GET - POST form model
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FormActionBuilder extends AbstractBuilder
{
    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Action $action)
    {
        $builder->addRoute($action, $action->getName(), array('GET', 'POST'), $action->getUriPath());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Action $action, UrlGeneratorInterface $generator, $data)
    {
        if (!$builder->getAction()) {
            $builder
                ->setMethod('POST')
                ->setAction($generator->generate(
                    $action->getRouteName(),
                    $action->getRouteParams($data)
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'form' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'form';
    }
}