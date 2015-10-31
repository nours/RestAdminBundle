<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Table\Field;

use Nours\RestAdminBundle\Domain\Action;
use Nours\RestAdminBundle\Domain\Resource;
use Nours\RestAdminBundle\Helper\AdminHelper;
use Nours\TableBundle\Field\AbstractFieldType;
use Nours\TableBundle\Field\FieldInterface;
use Nours\TableBundle\Table\View;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminActionsType
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminActionsType extends AbstractFieldType
{
    /**
     * @var AdminHelper
     */
    private $helper;

    public function __construct(AdminHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(View $view, FieldInterface $field, array $options)
    {
        $view->vars['actions']  = $options['actions'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'resource' => function(Options $options) {
                return $this->helper->getCurrentResource();
            },
            'actions'  => array(),
            'label'    => 'actions',
            'width'    => function(Options $options) {
                return count($options['actions']) * 50 + 16;
            }
        ));

        $resolver->setAllowedTypes('actions', array('array'));

        $resolver->setNormalizer('resource', function(Options $options, $resource) {
            if (null === $resource) {
                return null;
            }
            if (!$resource instanceof Resource) {
                $resource = $this->helper->getResource($resource);
            }
            return $resource;
        });

        $resolver->setNormalizer('actions', function(Options $options, $actions) {
            foreach ($actions as &$action) {
                if (!$action instanceof Action) {
                    if (strpos($action, ':') !== false) {
                        $action = $this->helper->getAction($action);
                    } elseif ($resource = $options['resource']) {
                        $action = $resource->getAction($action);
                    } else {
                        throw new \DomainException(sprintf(
                            "Please provide resource option to get %s action",
                            $action
                        ));
                    }
                }
            }

            return $actions;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'prototype';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_actions';
    }
}