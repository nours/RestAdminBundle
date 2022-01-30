<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Form;

use DomainException;
use Nours\RestAdminBundle\ActionManager;
use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Event\FormActionEvent;
use Nours\RestAdminBundle\Event\RestAdminEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
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

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(
        ActionManager $manager,
        FormFactoryInterface $factory,
        UrlGeneratorInterface $generator,
        EventDispatcherInterface $dispatcher
    ) {
        $this->manager = $manager;
        $this->factory = $factory;
        $this->generator = $generator;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Creates a form for a certain type
     *
     * @param mixed $data
     * @param Action $action
     * @param array $options
     * @return FormInterface
     */
    public function createForm($data, Action $action, array $options = []): FormInterface
    {
        // Find form name
        $formName = $action->getForm();

        if (empty($formName)) {
            throw new DomainException(sprintf(
                "Missing form for action %s",
                $action->getFullName()
            ));
        }

        // Find action builder
        $actionBuilder = $this->manager->getActionBuilder($action->getType());

        // Create builder
        $builder = $this->factory->createNamedBuilder($action->getResource()->getParamName(), $formName, $data, $options);

        $actionBuilder->buildForm($builder, $action, $this->generator, $data);

        $this->dispatcher->dispatch(new FormActionEvent($action, $builder, $data), RestAdminEvents::FORM);

        return $builder->getForm();
    }
}