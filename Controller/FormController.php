<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Controller;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\ResourceDataFactory;
use Nours\RestAdminBundle\Form\ActionFormFactory;
use Nours\RestAdminBundle\Form\FormSuccessHandler;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class FormController
 *
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class FormController
{
    /**
     * @var ResourceDataFactory
     */
    private $dataFactory;

    /**
     * @var ActionFormFactory
     */
    private $formFactory;
    /**
     * @var FormSuccessHandler
     */
    private $formSuccessHandler;

    public function __construct(
        ResourceDataFactory $dataFactory,
        ActionFormFactory $formFactory,
        FormSuccessHandler $formSuccessHandler
    )
    {
        $this->dataFactory = $dataFactory;
        $this->formFactory = $formFactory;
        $this->formSuccessHandler = $formSuccessHandler;
    }

    public function __invoke(Request $request, Action $action)
    {
        // Initialize data if not found from routing
        $data = $this->dataFactory->handle($request);

        // Create form
        $form = $this->formFactory->createForm($data, $action);

        if ($request->getMethod() == $form->getConfig()->getMethod()) {
            // Handle request only if method matches
            $form->handleRequest($request);

            if ($form->isValid()) {
                return $this->formSuccessHandler->handle($data, $request, $form, $action);
            }
        }

        return $form;
    }
}