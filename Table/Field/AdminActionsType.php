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
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Helper\AdminHelper;
use Nours\TableBundle\Field\AbstractFieldType;
use Nours\TableBundle\Field\FieldInterface;
use Nours\TableBundle\Field\Type\PrototypeType;
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
        /** @var Action[] $actions */
        $actions = $options['actions'];
        $view->vars['actions'] = $actions;

        $labels = $attrs = [];
        foreach ($actions as $index => $action) {
            $label = $options['action_label']($action);
            $labels[$index] = $label;

            $attr = $options['action_attr']($action);
            if (isset($options['attr_by_actions'][$action->getName()])) {
                $attr = array_merge($attr, $options['attr_by_actions'][$action->getName()]($action));
            }
            $attrs[$index] = $attr;
        }

        $view->vars['action_labels'] = $labels;
        $view->vars['action_attrs']  = $attrs;
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
            },
            'action_label' => function(Action $action) {
                return $action->getName();
            },
            'action_attr' => function() {
                return [];
            },
            'attr_by_actions' => []
        ));

        $resolver->setAllowedTypes('actions', array('array'));

        $resolver->setNormalizer('resource', function(Options $options, $resource) {
            if (null === $resource) {
                return null;
            }
            if (!$resource instanceof DomainResource) {
                $resource = $this->helper->getResource($resource);
            }
            return $resource;
        });

        $resolver->setNormalizer('actions', function(Options $options, $actions) {
            /** @var DomainResource $resource */
            $resource = $options['resource'];

            foreach ($actions as &$action) {
                if (!$action instanceof Action) {
                    if (empty($resource)) {
                        throw new \DomainException(sprintf(
                            "Please provide resource option to get %s action for admin_action field",
                            $action
                        ));
                    }

                    if (false === strpos($action, ':')) {
                        // Look for action in current resource
                        $action = $resource->getAction($action);
                    } else {
                        // TODO : implement a less magical behavior here, like :
                        // return $this->helper->getAction($action);
                        //
                        // Look for action in child resource
                        $exploded = explode(':', $action);
                        if ($resource->hasChild($exploded[0])) {
                            $childResource = $resource->getChild($exploded[0]);
                            $action = $childResource->getAction($exploded[1]);
                        } else {
                            $action = $this->helper->getAction($action);
                        }
                    }
                }
            }

            return $actions;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): ?string
    {
        return PrototypeType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'admin_actions';
    }
}