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

use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class ActionFormFactory.
 *
 * Generates unnamed forms from resource and action. The action builder has part in the form building process.
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class ActionFormFactory
{
    /**
     * @var ActionManager
     */
    private $manager;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var UrlGeneratorInterface
     */
    private $generator;

    public function __construct(ActionManager $manager, FormFactoryInterface $factory, UrlGeneratorInterface $generator)
    {
        $this->manager = $manager;
        $this->factory = $factory;
        $this->generator = $generator;
    }

    /**
     * Creates a form for a certain type
     *
     * @param \Nours\RestAdminBundle\Domain\Resource $resource
     * @param Action $action
     * @param $model
     * @return \Symfony\Component\Form\Form
     */
    public function createForm(Resource $resource, Action $action, $model)
    {
        // Find form name
        $formName = $action->getForm() ?: $resource->getForm();

        if (empty($formName)) {
            throw new \DomainException(sprintf(
                "Missing form for resource %s, action %s",
                $resource->getFullName(), $action->getName()
            ));
        }

        // Find action builder
        $actionBuilder = $this->manager->getActionBuilder($action->getName());

        // Create builder
        $builder = $this->factory->createNamedBuilder('', $formName, $model);

        $actionBuilder->buildForm($builder, $resource, $this->generator, $model);

        return $builder->getForm();
    }
}