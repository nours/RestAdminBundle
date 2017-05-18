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
            'route_params' => array()
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
            $resolver->setNormalizer('query_builder', function(Options $options, $queryBuilder)
            {
                /** @var DomainResource $resource */
                if ($resource = $options['resource']) {
                    $parentResource = $resource->getParent();

                    $parentData = $options['route_data'];

                    if ($parentResource && $parentData) {
                        $parentName = $parentResource->getParamName();

                        /** @var QueryBuilder $queryBuilder */
                        $queryBuilder
                            ->andWhere('_root.' . $parentName . ' = :parentData')
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