<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Event;

use Nours\RestAdminBundle\Domain\Action;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class FormActionEvent
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FormActionEvent extends ResourceEvent
{
    private $formBuilder;
    private $data;

    /**
     * FormActionEvent constructor.
     *
     * @param Action $action
     * @param FormBuilderInterface $formBuilder
     * @param mixed $data
     */
    public function __construct(Action $action, FormBuilderInterface $formBuilder, $data)
    {
        parent::__construct($action->getResource(), $action);

        $this->formBuilder = $formBuilder;
        $this->data        = $data;
    }

    /**
     * @return FormBuilderInterface
     */
    public function getFormBuilder(): FormBuilderInterface
    {
        return $this->formBuilder;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}