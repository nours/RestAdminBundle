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
    protected $form;

    /**
     * @param $template
     * @param $controller
     */
    public function __construct($template = null, $controller = null, $form = null)
    {
        parent::__construct($template, $controller);

        $this->form   = $form;
    }

    /**
     * {@inheritdoc}
     */
    public function buildRoutes(RoutesBuilder $builder, Resource $resource, Action $action)
    {
        $builder->addRoute($resource, $action, 'delete', 'GET', $resource->getResourceUriPath('delete'));
        $builder->addRoute($resource, $action, 'remove', 'DELETE', $resource->getResourceUriPath());
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, Resource $resource, UrlGeneratorInterface $generator, $data)
    {
        $routeName = $resource->getRouteName('remove');

        $builder
            ->setMethod('DELETE')
            ->setAction($generator->generate($routeName, $resource->getResourceRouteParams($data)));
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        // Sets default form for deleting objects
        $resolver->setDefaults(array(
            'form' => $this->form
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'delete';
    }
}