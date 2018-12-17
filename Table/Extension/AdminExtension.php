<?php
/*
 * This file is part of RestAdminBundle.
 *
 * (c) David Coudrier <david.coudrier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nours\RestAdminBundle\Table\Extension;

use Doctrine\ORM\QueryBuilder;
use Nours\RestAdminBundle\Domain\DomainResource;
use Nours\RestAdminBundle\Helper\AdminHelper;
use Nours\TableBundle\Extension\AbstractExtension;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AdminExtension
 * 
 * @author David Coudrier <david.coudrier@gmail.com>
 */
class AdminExtension extends AbstractExtension
{
    private $helper;
    private $disableParentFilter;

    public function __construct(AdminHelper $helper, $disableParentFilter = false)
    {
        $this->helper = $helper;
        $this->disableParentFilter = $disableParentFilter;
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
            'parent_resource' => function(Options $options) {
                return ($resource = $options['resource']) ? $resource->getParent() : null;
            },
            'class'  => function(Options $options) {
                $resource = $options['resource'];
                return $resource ? $resource->getClass() : null;
            },
            'action' => 'index',
            'url'    => function(Options $options) {
                if ($action = $options['action']) {
                    return $this->helper->generateUrl($action, $options['route_data'], $options['route_params']);
                }
                return null;
            },
            'route_data' => null,
            'route_params' => array(),
            'parent_param_name' => function(Options $options) {
                /** @var DomainResource $resource */
                if ($resource = $options['resource']) {
                    return $resource->getParentPropertyPath();
                }

                return ($resource = $options['parent_resource']) ? $resource->getParamName() : null;
            },
            'parent_property_path' => function(Options $options) {
                /** @var DomainResource $resource */
                if ($resource = $options['resource']) {
                    if ($propertyPath =  $resource->getConfig('parent_property_path')) {
                        return $propertyPath;
                    } else {
                        // BC with parent_param_name
                        return $options['parent_param_name'];
                    }
                }

                return null;
            }
        ));

        $resolver->setNormalizer('resource', function(Options $options, $value) {
            if (is_string($value)) {
                return $this->helper->getResource($value);
            }
            return $value;
        });
        $resolver->setNormalizer('action', function(Options $options, $value) {
            if (is_string($value)) {
                if ($resource = $options['resource']) {
                    return $resource->getAction($value);
                }
            }
            return null;
        });

        /*
         * Add to query builder a filter for resources having parents.
         */
        if (!$this->disableParentFilter) {
            $resolver->setNormalizer('query_builder', function(Options $options, $queryBuilder) {
                if ($options['parent_resource']) {
                    $parentData  = $options['route_data'];
                    $parentField = $options['parent_property_path'];

                    if ($parentField && $parentData) {
                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder
                            ->andWhere('_root.' . $parentField . ' = :parentData')
                            ->setParameter('parentData', $parentData)
                        ;
                    }
                }

                return $queryBuilder;
            });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDependency()
    {
        return 'orm';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin';
    }
}